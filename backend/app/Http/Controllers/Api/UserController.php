<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Mostrar usuarios con paginación, búsqueda y filtro por rol.
     *
     * Ejemplo:
     * GET /api/users?search=juan&role=Cliente&page=1&per_page=10
     */
    public function index(Request $request)
    {
        // Se permiten entre 1 y 50 registros por página.
        $perPage = (int) $request->query('per_page', 10);
        $perPage = min(max($perPage, 1), 50);

        $search = trim(
            (string) $request->query('search', '')
        );

        $role = $request->query('role');

        $usuarios = User::query()

            // Buscar por nombre o correo electrónico.
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subconsulta) use ($search) {
                    $subconsulta
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })

            // Filtrar por rol cuando sea un rol permitido.
            ->when(
                in_array(
                    $role,
                    ['Administrador', 'Empleado', 'Cliente'],
                    true
                ),
                function ($query) use ($role) {
                    $query->where('role', $role);
                }
            )

            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return UserResource::collection($usuarios);
    }

    /**
     * Registrar un usuario desde el panel administrativo.
     */
    public function store(StoreUserRequest $request)
    {
        $datos = $request->validated();

        // Solamente los clientes pueden tener vencimiento de membresía.
        if ($datos['role'] !== 'Cliente') {
            $datos['fecha_vencimiento'] = null;
        }

        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'role' => $datos['role'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
        ]);

        return (new UserResource($usuario))
            ->additional([
                'message' => 'Usuario registrado correctamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar un usuario específico.
     *
     * Laravel regresará automáticamente un error 404
     * cuando el identificador no exista.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Actualizar los datos de un usuario.
     */
    public function update(
        UpdateUserRequest $request,
        User $user
    ) {
        $datos = $request->validated();

        /*
         * Evita que el administrador autenticado cambie su propio rol
         * y pierda inmediatamente el acceso administrativo.
         */
        if (
            $request->user()->is($user) &&
            isset($datos['role']) &&
            $datos['role'] !== $user->role
        ) {
            return response()->json([
                'message' => 'No puedes cambiar el rol de tu propia cuenta mientras estás autenticado.',
            ], 422);
        }

        /*
         * Si no se envió una contraseña, se conserva la actual.
         */
        if (
            !array_key_exists('password', $datos) ||
            empty($datos['password'])
        ) {
            unset($datos['password']);
        } else {
            $datos['password'] = Hash::make($datos['password']);
        }

        /*
         * Administradores y empleados no deben tener una fecha
         * de vencimiento de membresía.
         */
        $roleFinal = $datos['role'] ?? $user->role;

        if ($roleFinal !== 'Cliente') {
            $datos['fecha_vencimiento'] = null;
        }

        $user->update($datos);

        return (new UserResource($user->fresh()))
            ->additional([
                'message' => 'Usuario actualizado correctamente.',
            ]);
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(
        Request $request,
        User $user
    ): JsonResponse {
        /*
         * Evita que el administrador elimine su propia cuenta.
         */
        if ($request->user()->is($user)) {
            return response()->json([
                'message' => 'No puedes eliminar tu propia cuenta.',
            ], 422);
        }

        /*
         * Evita eliminar al último administrador disponible.
         */
        if (
            $user->role === 'Administrador' &&
            User::where('role', 'Administrador')->count() <= 1
        ) {
            return response()->json([
                'message' => 'No se puede eliminar al último administrador del sistema.',
            ], 409);
        }

        /*
         * Evita errores de integridad cuando el usuario
         * ya tiene ventas relacionadas.
         */
        $tieneVentas = Venta::query()
            ->where('id_empleado', $user->id)
            ->orWhere('id_cliente', $user->id)
            ->exists();

        if ($tieneVentas) {
            return response()->json([
                'message' => 'No se puede eliminar el usuario porque tiene ventas relacionadas.',
            ], 409);
        }

        // Elimina también sus tokens activos de Sanctum.
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente.',
        ], 200);
    }
}