<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCourier
{
    public function handle(Request $request, Closure $next): Response
    {
        $courier = $request->session()->get('repartidor', []);
        $isCourier = is_array($courier)
            && (int) ($courier['id_usuario'] ?? 0) > 0
            && (int) ($courier['id_repartidor'] ?? 0) > 0
            && strtolower((string) $request->session()->get('usuario_rol', '')) === 'repartidor';

        if (! $isCourier) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Debes iniciar sesión como repartidor.'], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
