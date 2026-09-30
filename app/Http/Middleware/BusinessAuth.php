<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->session()->get('business_user', []);

        if (
            ! is_array($user)
            || strtolower((string) ($user['rol'] ?? '')) !== 'negocio'
            || (int) ($user['id_usuario'] ?? 0) < 1
            || (int) ($user['id_negocio'] ?? 0) < 1
        ) {
            $request->session()->forget('business_user');

            return redirect()->route('login');
        }

        return $next($request);
    }
}
