<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function pedidos()
    {
        return view('pages.pedidos');
    }

    public function activos()
    {
        return view('pages.activos');
    }

    public function historial()
    {
        return view('pages.historial');
    }

    public function notificaciones(Request $request)
    {
        $userId = (int) $request->session()->get('id_usuario', $request->session()->get('repartidor.id_usuario', 0));
        if ($userId <= 0) {
            if ($request->wantsJson() || $request->is('*/php/notificaciones.php')) {
                return response()->json(['error' => 'No autenticado', 'items' => []], 401);
            }
            return redirect()->route('login');
        }

        if ($request->isMethod('post')) {
            DB::table('notificacion')
                ->where('id_usuario', $userId)
                ->where('audiencia', 'repartidor')
                ->update(['leida' => 1]);

            if ($request->wantsJson() || $request->is('*/php/notificaciones.php')) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'Notificaciones marcadas como leídas.');
        }

        $items = DB::table('notificacion')
            ->where('id_usuario', $userId)
            ->where('audiencia', 'repartidor')
            ->orderByDesc('fecha_creacion')
            ->limit(50)
            ->get();

        if ($request->wantsJson() || $request->is('*/php/notificaciones.php') || $request->header('Accept') === 'application/json') {
            return response()->json([
                'items' => $items,
                'unread' => $items->where('leida', 0)->count(),
            ]);
        }

        return view('pages.notificaciones', compact('items'));
    }
}
