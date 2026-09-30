<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntregaController extends Controller
{
    private function generarCodigoRepartidor(int $idEntrega): string
    {
        return sprintf('%06d', ($idEntrega * 265443) % 900000 + 100000);
    }

    private function getCourier(Request $request): ?object
    {
        $courierSession = $request->session()->get('repartidor', []);
        if (! is_array($courierSession)
            || strtolower((string) $request->session()->get('usuario_rol', '')) !== 'repartidor') {
            return null;
        }

        $userId = (int) ($courierSession['id_usuario'] ?? 0);
        $courierId = (int) ($courierSession['id_repartidor'] ?? 0);
        if ($userId <= 0 || $courierId <= 0) {
            return null;
        }

        return DB::table('repartidor')
            ->where('id_usuario', $userId)
            ->where('id_repartidor', $courierId)
            ->first();
    }

    public function index(Request $request): JsonResponse
    {
        $courier = $this->getCourier($request);
        if (! $courier) {
            return response()->json(['error' => 'Debes iniciar sesión como repartidor.'], 401);
        }

        $courierId = (int) $courier->id_repartidor;
        $action = (string) $request->input('action', $request->query('action', ''));
        $type = (string) $request->query('type', $request->input('type', 'available'));

        if ($action === 'stats' || $type === 'stats') {
            $stats = DB::table('repartidor as r')
                ->join('usuario as u', 'u.id_usuario', '=', 'r.id_usuario')
                ->leftJoin('entrega as e', 'e.id_repartidor', '=', 'r.id_repartidor')
                ->leftJoin('compra as c', 'c.id_compra', '=', 'e.id_compra')
                ->leftJoin('calificacion_repartidor as cr', 'cr.id_repartidor', '=', 'r.id_repartidor')
                ->where('r.id_repartidor', $courierId)
                ->selectRaw(
                    'u.nombre, u.apellido,
                     COUNT(CASE WHEN e.fecha_entrega IS NOT NULL AND DATE(e.fecha_entrega) = CURDATE() THEN 1 END) AS entregas_hoy,
                     COALESCE(SUM(CASE WHEN e.fecha_entrega IS NOT NULL AND DATE(e.fecha_entrega) = CURDATE() THEN c.total ELSE 0 END), 0) AS ganancias_hoy,
                     COALESCE(AVG(cr.puntuacion), 0) AS calificacion'
                )
                ->groupBy('r.id_repartidor', 'u.nombre', 'u.apellido')
                ->first();

            return response()->json([
                'nombre' => $stats->nombre ?? '',
                'apellido' => $stats->apellido ?? '',
                'entregasHoy' => (int) ($stats->entregas_hoy ?? 0),
                'gananciasHoy' => (float) ($stats->ganancias_hoy ?? 0),
                'calificacion' => round((float) ($stats->calificacion ?? 0), 1),
            ]);
        }

        if ($action === 'toggle') {
            $current = (bool) $request->session()->get('repartidor_online', true);
            $request->session()->put('repartidor_online', ! $current);
            return response()->json(['online' => ! $current]);
        }

        $whereClause = match ($type) {
            'history' => 'e.id_repartidor = ? AND e.fecha_entrega IS NOT NULL',
            'active' => 'e.id_repartidor = ? AND e.fecha_entrega IS NULL AND e.fecha_confirmacion IS NULL',
            default => 'e.id_repartidor IS NULL AND e.estado = "Pendiente"',
        };

        $bindings = ($type === 'available') ? [] : [$courierId];

        $items = DB::select(
            "SELECT e.id_entrega, e.estado AS entrega_estado, e.fecha_entrega, e.codigo_entrega,
                    c.total, p.id_pedido, p.direccion_entrega, p.estado AS pedido_estado,
                    p.tiempo_preparacion, p.hora_estimada_listo,
                    u.nombre AS cliente_nombre, u.apellido AS cliente_apellido,
                    n.nombre AS negocio_nombre
             FROM entrega e
             JOIN compra c ON c.id_compra = e.id_compra
             JOIN pedido p ON p.id_pedido = c.id_pedido
             JOIN usuario u ON u.id_usuario = p.id_usuario
             JOIN negocios n ON n.id_negocio = p.id_negocio
             WHERE {$whereClause}
             ORDER BY p.fecha_creacion DESC",
            $bindings
        );

        $pedidoIds = array_values(array_unique(array_map(
            static fn ($item): int => (int) $item->id_pedido,
            $items
        )));

        $detallesPorPedido = [];
        if (! empty($pedidoIds)) {
            $detalles = DB::table('detalle_pedido as d')
                ->join('menu_items as m', 'm.id_menu_item', '=', 'd.id_menu_item')
                ->whereIn('d.id_pedido', $pedidoIds)
                ->select('d.id_pedido', 'd.cantidad', 'm.nombre')
                ->orderBy('d.id_pedido')
                ->orderBy('m.nombre')
                ->get();

            foreach ($detalles as $d) {
                $detallesPorPedido[(int) $d->id_pedido][] = [
                    'nombre' => (string) $d->nombre,
                    'cantidad' => (int) $d->cantidad,
                ];
            }
        }

        foreach ($items as &$item) {
            $item->id = (int) $item->id_entrega;
            $item->codigo_c = (string) $item->codigo_entrega;
            $item->codigo_recogida = $this->generarCodigoRepartidor((int) $item->id_entrega);
            $item->detalles = $detallesPorPedido[(int) $item->id_pedido] ?? [];

            if (! empty($item->hora_estimada_listo)) {
                $tsListo = strtotime((string) $item->hora_estimada_listo);
                $diff = $tsListo - time();
                $item->segundos_restantes = max(0, $diff);
                $item->minutos_restantes = (int) ceil(max(0, $diff) / 60);
                $item->esta_vencido = ($diff <= 0);
            } else {
                $item->segundos_restantes = null;
                $item->minutos_restantes = null;
                $item->esta_vencido = false;
            }
        }
        unset($item);

        return response()->json([
            'online' => (bool) $request->session()->get('repartidor_online', true),
            'items'  => $items,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $courier = $this->getCourier($request);
        if (! $courier) {
            return response()->json(['error' => 'Debes iniciar sesión como repartidor.'], 401);
        }

        $courierId = (int) $courier->id_repartidor;
        $data = $request->validate([
            'action' => ['required', 'in:toggle,accept,advance,deliver_client_code'],
            'id_entrega' => ['required_unless:action,toggle', 'integer', 'min:1'],
            'codigo_cliente' => ['required_if:action,deliver_client_code', 'regex:/^[0-9]{6}$/'],
        ]);
        $action = (string) $data['action'];
        $deliveryId = (int) ($data['id_entrega'] ?? 0);

        if ($action === 'toggle') {
            $current = (bool) $request->session()->get('repartidor_online', true);
            $request->session()->put('repartidor_online', ! $current);
            return response()->json(['online' => ! $current]);
        }

        if ($action === 'accept') {
            $codigoR = $this->generarCodigoRepartidor($deliveryId);

            $updated = DB::transaction(function () use ($courierId, $deliveryId): int {
                DB::table('repartidor')->where('id_repartidor', $courierId)->lockForUpdate()->first();
                $activeCount = DB::table('entrega')
                    ->where('id_repartidor', $courierId)
                    ->whereNull('fecha_confirmacion')
                    ->whereNull('fecha_entrega')
                    ->count();

                if ($activeCount >= 4) {
                    return -1;
                }

                return DB::table('entrega')
                    ->where('id_entrega', $deliveryId)
                    ->whereNull('id_repartidor')
                    ->where('estado', 'Pendiente')
                    ->update([
                        'id_repartidor' => $courierId,
                        'fecha_asignacion' => now(),
                        'estado' => 'Asignado',
                    ]);
            });

            if ($updated === -1) {
                return response()->json(['error' => 'Ya tienes el máximo de cuatro pedidos activos.'], 422);
            }
            if ($updated !== 1) {
                return response()->json(['error' => 'El pedido ya fue tomado por otro repartidor.'], 409);
            }

            $recipient = DB::table('entrega as e')
                ->join('compra as c', 'c.id_compra', '=', 'e.id_compra')
                ->join('pedido as p', 'p.id_pedido', '=', 'c.id_pedido')
                ->join('negocios as n', 'n.id_negocio', '=', 'p.id_negocio')
                ->where('e.id_entrega', $deliveryId)
                ->select(
                    'p.id_usuario as cliente_id',
                    'n.id_usuario as negocio_id',
                    'p.id_pedido',
                    'p.estado as pedido_estado',
                    'p.tiempo_preparacion',
                    'p.hora_estimada_listo'
                )
                ->first();

            if ($recipient) {
                DB::table('notificacion')->insert([
                    [
                        'id_usuario' => (int) $recipient->cliente_id,
                        'audiencia' => 'cliente',
                        'id_pedido' => (int) $recipient->id_pedido,
                        'tipo' => 'repartidor_acepto',
                        'titulo' => 'Tu pedido fue aceptado',
                        'mensaje' => 'Un repartidor aceptó tu pedido y pronto lo recogerá.',
                    ],
                    [
                        'id_usuario' => (int) $recipient->negocio_id,
                        'audiencia' => 'negocio',
                        'id_pedido' => (int) $recipient->id_pedido,
                        'tipo' => 'repartidor_acepto',
                        'titulo' => 'Repartidor asignado',
                        'mensaje' => 'Un repartidor aceptó el pedido y puede recogerlo cuando esté preparado.',
                    ],
                ]);

                $courierUid = (int) ($courier->id_usuario ?? 0);
                if ($courierUid > 0) {
                    if (strtolower((string) $recipient->pedido_estado) === 'preparado') {
                        DB::table('notificacion')->insert([
                            'id_usuario' => $courierUid,
                            'audiencia' => 'repartidor',
                            'id_pedido' => (int) $recipient->id_pedido,
                            'tipo' => 'pedido_preparado',
                            'titulo' => '✅ ¡Pedido listo para recoger!',
                            'mensaje' => 'El pedido #' . (int) $recipient->id_pedido . ' ya está preparado y puedes recogerlo en el negocio.',
                        ]);
                    } elseif (! empty($recipient->tiempo_preparacion)) {
                        DB::table('notificacion')->insert([
                            'id_usuario' => $courierUid,
                            'audiencia' => 'repartidor',
                            'id_pedido' => (int) $recipient->id_pedido,
                            'tipo' => 'tiempo_preparacion',
                            'titulo' => '⏱️ Preparación: ' . (int) $recipient->tiempo_preparacion . ' min',
                            'mensaje' => 'El pedido #' . (int) $recipient->id_pedido . ' estará listo en aproximadamente ' . (int) $recipient->tiempo_preparacion . ' minutos.',
                        ]);
                    }
                }
            }

            return response()->json([
                'updated' => true,
                'codigo_r' => $codigoR,
                'mensaje' => '¡Pedido aceptado! Muestra este código al negocio: ' . $codigoR,
            ]);
        }

        $delivery = DB::table('entrega as e')
            ->join('compra as c', 'c.id_compra', '=', 'e.id_compra')
            ->join('pedido as p', 'p.id_pedido', '=', 'c.id_pedido')
            ->where('e.id_entrega', $deliveryId)
            ->where('e.id_repartidor', $courierId)
            ->select('e.*', 'p.id_pedido')
            ->first();

        if (! $delivery) {
            return response()->json(['error' => 'Entrega no encontrada.'], 404);
        }

        if ($action === 'advance') {
            if ($delivery->fecha_entrega) {
                return response()->json(['status' => 'already_completed']);
            }

            if ((string) $delivery->estado !== 'Recogido en negocio') {
                return response()->json(['error' => 'El negocio aún no valida la recogida del pedido.'], 409);
            }

            $updated = DB::transaction(function () use ($deliveryId, $delivery): int {
                $updated = DB::table('entrega')
                    ->where('id_entrega', $deliveryId)
                    ->where('id_repartidor', $delivery->id_repartidor)
                    ->where('estado', 'Recogido en negocio')
                    ->whereNull('fecha_entrega')
                    ->update(['estado' => 'En camino']);
                if ($updated !== 1) {
                    return 0;
                }
                DB::table('pedido')->where('id_pedido', $delivery->id_pedido)->update(['estado' => 'En camino']);
                return $updated;
            });

            if ($updated !== 1) {
                return response()->json(['error' => 'La entrega ya cambió de estado. Actualiza la página e inténtalo nuevamente.'], 409);
            }

            return response()->json(['status' => 'picked_up', 'updated' => true]);
        }

        if ($action === 'deliver_client_code') {
            $code = trim((string) $data['codigo_cliente']);
            $codigoEsperado = trim((string) $delivery->codigo_entrega);

            if ($delivery->estado !== 'En camino' || ! empty($delivery->fecha_confirmacion) || ! hash_equals($codigoEsperado, $code)) {
                return response()->json(['error' => 'El código ingresado es incorrecto o la entrega no está en camino.'], 422);
            }

            DB::transaction(function () use ($deliveryId, $delivery): void {
                $upd = DB::table('entrega')
                    ->where('id_entrega', $deliveryId)
                    ->where('estado', 'En camino')
                    ->whereNull('fecha_confirmacion')
                    ->update([
                        'estado' => 'Entregado',
                        'fecha_entrega' => now(),
                        'fecha_confirmacion' => now(),
                    ]);

                if ($upd !== 1) {
                    throw new \RuntimeException('La entrega ya fue confirmada o no está en camino.');
                }

                DB::table('pedido')->where('id_pedido', $delivery->id_pedido)->update(['estado' => 'Entregado']);
                DB::table('compra')->where('id_pedido', $delivery->id_pedido)->update(['estado' => 'Completado']);
            });

            return response()->json(['status' => 'delivered', 'updated' => true]);
        }

        return response()->json(['error' => 'Acción no válida.'], 422);
    }
}
