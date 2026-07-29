<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registrar una nueva cuenta.
     *
     * Los registros públicos siempre tendrán el rol Cliente.
     * Los empleados y administradores se crearán posteriormente
     * desde el módulo administrativo.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $usuario = User::create([
            'name' => $datos['name'],
            'email' => strtolower($datos['email']),
            'password' => Hash::make($datos['password']),
            'role' => 'Cliente',
        ]);

        $token = $usuario
            ->createToken('gymhub-web')
            ->plainTextToken;

        return response()->json([
            'message' => 'Cuenta creada correctamente.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $usuario,
        ], 201);
    }

    /**
     * Iniciar sesión mediante correo y contraseña.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $usuario = User::where(
            'email',
            strtolower($datos['email'])
        )->first();

        if (
            !$usuario ||
            !Hash::check($datos['password'], $usuario->password)
        ) {
            return response()->json([
                'message' => 'Credenciales incorrectas. Verifica tu correo y contraseña.',
            ], 401);
        }

        $token = $usuario
            ->createToken('gymhub-web')
            ->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $usuario,
        ], 200);
    }

    /**
     * Obtener los datos del usuario autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Usuario autenticado obtenido correctamente.',
            'user' => $request->user(),
        ], 200);
    }

    /**
     * Cerrar la sesión actual eliminando el token utilizado.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ->currentAccessToken()
            ?->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ], 200);
    }
}