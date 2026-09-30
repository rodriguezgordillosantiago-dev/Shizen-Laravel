<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->session()->get('auth_user');
        if (!is_array($user) || empty($user['id_usuario'])) {
            if ($request->expectsJson() || $request->boolean('json')) {
                return response()->json(['success' => false, 'unread_count' => 0, 'notifications' => []], 401);
            }

            return redirect()->route('login', ['redirect' => '/notificaciones']);
        }

        $notifications = DB::table('notificacion')
            ->where('id_usuario', $user['id_usuario'])
            ->where('audiencia', 'cliente')
            ->orderByDesc('fecha_creacion')
            ->get(['id_notificacion', 'id_pedido', 'tipo', 'titulo', 'mensaje', 'leida', 'fecha_creacion']);

        if ($request->expectsJson() || $request->boolean('json')) {
            return response()->json([
                'success' => true,
                'unread_count' => $notifications->where('leida', 0)->count(),
                'notifications' => $notifications,
            ]);
        }

        return view('notificaciones', compact('notifications'));
    }

    public function markRead(Request $request)
    {
        $user = $request->session()->get('auth_user');
        abort_unless(is_array($user) && !empty($user['id_usuario']), 403);

        DB::table('notificacion')
            ->where('id_usuario', $user['id_usuario'])
            ->where('audiencia', 'cliente')
            ->update(['leida' => 1]);

        return back();
    }
}
