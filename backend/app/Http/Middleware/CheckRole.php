<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verifica que el usuario autenticado tenga uno
     * de los roles permitidos para la ruta.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'message' => 'No has iniciado sesión.',
            ], 401);
        }

        if (!in_array($usuario->role, $roles, true)) {
            return response()->json([
                'message' => 'Acceso denegado. Tu rol no tiene permiso para realizar esta acción.',
                'role_actual' => $usuario->role,
                'roles_permitidos' => $roles,
            ], 403);
        }

        return $next($request);
    }
}