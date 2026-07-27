<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Verificamos si hay un usuario logueado y si su rol coincide con el requerido
        if (! $request->user() || $request->user()->role !== $role) {
            
            // 2. Si no coincide, lo rebotamos con un error 403 (Prohibido)
            return response()->json([
                'message' => 'Acceso denegado. Tu nivel de usuario no permite ver esta sección.'
            ], 403);
        }

        // 3. Si todo está bien, lo dejamos pasar
        return $next($request);
    }
}