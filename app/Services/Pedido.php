<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class Pedido
{
    public function forUser(int $userId): Collection
    {
        return DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->leftJoin('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->leftJoin('negocios as n', 'n.id_negocio', '=', 'p.id_negocio')
            ->where('p.id_usuario', $userId)
            ->select(
                'p.id_pedido',
                'p.descripcion',
                'p.direccion_entrega',
                'p.estado as pedido_estado',
                'p.fecha_creacion',
                'c.total',
                'c.estado as compra_estado',
                'e.estado as entrega_estado',
                'e.codigo_entrega',
                'e.fecha_confirmacion',
                'e.id_repartidor',
                'n.nombre as negocio_nombre'
            )
            ->orderByDesc('p.fecha_creacion')
            ->get();
    }

    public function create(int $userId, array $items, array $data): int
    {
        if (!$items) {
            throw new InvalidArgumentException('El carrito está vacío.');
        }

        $quantities = [];
        foreach ($items as $item) {
            $id = (int) ($item['id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($id < 1 || $quantity < 1 || $quantity > 99) {
                throw new RuntimeException('La cantidad de un plato no es válida.');
            }
            $quantities[$id] = ($quantities[$id] ?? 0) + $quantity;
            if ($quantities[$id] > 99) {
                throw new RuntimeException('La cantidad de un plato no es válida.');
            }
        }

        return DB::transaction(function () use ($userId, $quantities, $data): int {
            $products = DB::table('menu_items')
                ->whereIn('id_menu_item', array_keys($quantities))
                ->lockForUpdate()
                ->get(['id_menu_item', 'id_negocio', 'nombre', 'precio', 'stock', 'on_promo', 'precio_promocion'])
                ->keyBy('id_menu_item');

            if ($products->count() !== count($quantities)) {
                throw new RuntimeException('Uno de los platos ya no está disponible.');
            }

            $businessIds = $products->pluck('id_negocio')->unique();
            if ($businessIds->count() !== 1 || !$businessIds->first()) {
                throw new RuntimeException('Cada pedido debe corresponder a un solo negocio.');
            }

            $total = 0;
            $description = [];
            $orderItems = [];
            foreach ($quantities as $id => $quantity) {
                $product = $products[$id];
                if ($quantity > (int) $product->stock) {
                    throw new RuntimeException('No hay suficiente existencia de ' . $product->nombre . '.');
                }

                $unitPrice = (bool) $product->on_promo && (int) $product->precio_promocion > 0
                    ? (int) $product->precio_promocion
                    : (int) $product->precio;
                $total += $unitPrice * $quantity;
                $description[] = $quantity . 'x ' . $product->nombre;
                $orderItems[] = ['id' => (int) $id, 'quantity' => $quantity, 'price' => $unitPrice];
            }

            $businessId = (int) $businessIds->first();
            $address = trim($data['direccion'] . ' ' . ($data['localidad'] ?? ''));
            $orderId = ((int) DB::table('pedido')->lockForUpdate()->max('id_pedido')) + 1;
            $purchaseId = ((int) DB::table('compra')->lockForUpdate()->max('id_compra')) + 1;

            DB::table('pedido')->insert([
                'id_pedido' => $orderId,
                'id_usuario' => $userId,
                'id_negocio' => $businessId,
                'descripcion' => implode(', ', $description),
                'direccion_entrega' => $address,
                'estado' => 'Pendiente',
                'fecha_creacion' => now(),
            ]);

            foreach ($orderItems as $item) {
                DB::table('detalle_pedido')->insert([
                    'id_pedido' => $orderId,
                    'id_menu_item' => $item['id'],
                    'valor' => $item['price'],
                    'fecha' => now(),
                    'cantidad' => $item['quantity'],
                ]);
            }

            DB::table('compra')->insert([
                'id_compra' => $purchaseId,
                'id_usuario' => $userId,
                'id_pedido' => $orderId,
                'fecha_pago' => now(),
                'total' => $total,
                'metodo_pago' => $data['metodo_pago'],
                'estado' => 'Pendiente',
                'token_transaccion' => Str::random(32),
            ]);

            DB::table('entrega')->insert([
                'id_compra' => $purchaseId,
                'estado' => 'Pendiente',
                'codigo_entrega' => (string) random_int(100000, 999999),
            ]);

            DB::table('carrito')
                ->where('id_usuario', $userId)
                ->whereIn('id_menu_item', array_keys($quantities))
                ->delete();

            return $orderId;
        });
    }

    public function findForUser(int $userId, int $orderId): ?object
    {
        return DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->leftJoin('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->leftJoin('repartidor as r', 'r.id_repartidor', '=', 'e.id_repartidor')
            ->where('p.id_pedido', $orderId)
            ->where('p.id_usuario', $userId)
            ->select(
                'p.*',
                'c.id_compra',
                'c.total',
                'c.metodo_pago',
                'e.estado as entrega_estado',
                'e.codigo_entrega',
                'e.fecha_confirmacion',
                'e.id_repartidor',
                'r.nombre as repartidor_nombre',
                'r.apellido as repartidor_apellido'
            )
            ->first();
    }

    public function isRated(int $userId, int $orderId): bool
    {
        return DB::table('calificacion_repartidor')
            ->where('id_usuario', $userId)
            ->where('id_pedido', $orderId)
            ->exists();
    }

    public function isBusinessRated(int $userId, int $businessId): bool
    {
        return DB::table('calificacion')
            ->where('id_usuario', $userId)
            ->where('id_negocio', $businessId)
            ->exists();
    }

    public function confirmDelivery(int $userId, int $orderId, string $code): bool
    {
        $delivery = DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->join('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->where('p.id_pedido', $orderId)
            ->where('p.id_usuario', $userId)
            ->where('e.codigo_entrega', $code)
            ->whereNull('e.fecha_confirmacion')
            ->select('p.id_pedido', 'e.id_entrega')
            ->first();

        if (!$delivery) {
            return false;
        }

        DB::transaction(function () use ($delivery): void {
            DB::table('entrega')
                ->where('id_entrega', $delivery->id_entrega)
                ->update([
                    'estado' => 'Entregado',
                    'fecha_confirmacion' => now(),
                ]);
            DB::table('pedido')
                ->where('id_pedido', $delivery->id_pedido)
                ->update(['estado' => 'Recibido']);
        });

        return true;
    }

    public function rate(int $userId, int $orderId, int $score, ?string $comment): bool
    {
        $order = DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->join('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->where('p.id_pedido', $orderId)
            ->where('p.id_usuario', $userId)
            ->whereNotNull('e.fecha_confirmacion')
            ->select('p.id_negocio', 'e.id_entrega', 'e.id_repartidor')
            ->first();
        if (!$order) {
            return false;
        }

        $courierId = $order->id_repartidor;
        if (!$courierId) {
            $courierId = DB::table('repartidor')->orderBy('id_repartidor')->value('id_repartidor');
            if (!$courierId) {
                return false;
            }
            DB::table('entrega')
                ->where('id_entrega', $order->id_entrega)
                ->whereNull('id_repartidor')
                ->update(['id_repartidor' => $courierId]);
        }

        DB::table('calificacion_repartidor')->updateOrInsert(
            ['id_pedido' => $orderId],
            [
                'id_repartidor' => $courierId,
                'id_usuario' => $userId,
                'puntuacion' => $score,
                'comentario' => $comment,
                'fecha' => now(),
            ]
        );

        return true;
    }

    public function rateBusiness(int $userId, int $orderId, int $score, ?string $comment): bool
    {
        $order = DB::table('pedido as p')
            ->join('compra as c', 'c.id_pedido', '=', 'p.id_pedido')
            ->join('entrega as e', 'e.id_compra', '=', 'c.id_compra')
            ->where('p.id_pedido', $orderId)
            ->where('p.id_usuario', $userId)
            ->whereNotNull('e.fecha_confirmacion')
            ->value('p.id_negocio');

        if (!$order) {
            return false;
        }

        DB::table('calificacion')->updateOrInsert(
            ['id_usuario' => $userId, 'id_negocio' => $order],
            [
                'id_pedido' => $orderId,
                'puntuacion' => $score,
                'comentario' => $comment,
                'fecha' => now(),
            ]
        );

        return true;
    }
}
