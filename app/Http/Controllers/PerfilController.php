<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PerfilController extends Controller
{
    public function index(Request $request)
    {
        $userId = (int) $request->session()->get('id_usuario', $request->session()->get('repartidor.id_usuario', 0));

        $perfil = null;
        if ($userId > 0) {
            $profileColumns = [
                'u.id_usuario',
                'u.nombre',
                'u.apellido',
                'u.email',
                'r.id_repartidor',
                'r.vehiculo',
            ];
            foreach (['foto_url', 'licencia_conduccion_url', 'soat_url', 'tarjeta_propiedad_url'] as $optionalColumn) {
                $profileColumns[] = Schema::hasColumn('repartidor', $optionalColumn)
                    ? 'r.' . $optionalColumn
                    : DB::raw('NULL AS ' . $optionalColumn);
            }

            $perfil = DB::table('usuario as u')
                ->leftJoin('repartidor as r', 'r.id_usuario', '=', 'u.id_usuario')
                ->where('u.id_usuario', $userId)
                ->select($profileColumns)
                ->first();
        }

        $repartidor = [
            'nombre' => $perfil->nombre ?? $request->session()->get('repartidor.nombre', ''),
            'apellido' => $perfil->apellido ?? $request->session()->get('repartidor.apellido', ''),
            'email' => $perfil->email ?? $request->session()->get('repartidor.email', ''),
            'vehiculo' => $perfil->vehiculo ?? $request->session()->get('repartidor.vehiculo', ''),
            'foto_url' => $perfil->foto_url ?? null,
            'licencia_conduccion_url' => $perfil->licencia_conduccion_url ?? null,
            'soat_url' => $perfil->soat_url ?? null,
            'tarjeta_propiedad_url' => $perfil->tarjeta_propiedad_url ?? null,
        ];

        $stats = DB::table('repartidor as r')
            ->leftJoin('entrega as e', 'e.id_repartidor', '=', 'r.id_repartidor')
            ->leftJoin('compra as c', 'c.id_compra', '=', 'e.id_compra')
            ->where('r.id_usuario', $userId)
            ->selectRaw('
                COUNT(CASE WHEN e.fecha_entrega IS NOT NULL AND DATE(e.fecha_entrega) = CURDATE() THEN 1 END) AS entregas_hoy,
                COALESCE(SUM(CASE WHEN e.fecha_entrega IS NOT NULL AND DATE(e.fecha_entrega) = CURDATE() THEN c.total ELSE 0 END), 0) AS ganancias_hoy
            ')
            ->first();

        $entregasHoy = (int) ($stats->entregas_hoy ?? 0);
        $gananciasHoy = '$' . number_format((float) ($stats->ganancias_hoy ?? 0), 0, ',', '.');

        return view('pages.perfil', compact('repartidor', 'entregasHoy', 'gananciasHoy'));
    }

    public function configuracion()
    {
        return view('pages.configuracion', ['preferencias' => session('preferencias', [
            'notificaciones_push' => true, 'sonido_pedidos' => true,
        ])]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'apellido' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'vehiculo' => ['nullable', 'in:Moto,Bicicleta,Carro,Monopatín,monopatin'],
        ]);

        $userId = (int) $request->session()->get('id_usuario', $request->session()->get('repartidor.id_usuario', 0));
        if ($userId > 0) {
            DB::transaction(function () use ($data, $userId): void {
                DB::table('usuario')->where('id_usuario', $userId)->update([
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                ]);

                $repUpdate = [
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                ];
                if (! empty($data['vehiculo'])) {
                    $repUpdate['vehiculo'] = $data['vehiculo'];
                }
                DB::table('repartidor')->where('id_usuario', $userId)->update($repUpdate);
            });
        }

        $repartidor = array_merge($request->session()->get('repartidor', []), $data);
        $request->session()->put('repartidor', $repartidor);
        $request->session()->put('usuario_nombre', $data['nombre']);

        return redirect()->route('perfil')->with('status', 'Perfil actualizado correctamente.');
    }

    public function guardarConfiguracion(Request $request)
    {
        $request->session()->put('preferencias', [
            'notificaciones_push' => $request->boolean('notificaciones_push'),
            'sonido_pedidos' => $request->boolean('sonido_pedidos'),
        ]);
        return redirect()->route('configuracion')->with('status', 'Preferencias guardadas.');
    }
}
