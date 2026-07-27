<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validar que nos envíen email y password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Buscar al usuario en la base de datos
        $user = User::where('email', $request->email)->first();

        // 3. Revisar si el usuario existe y la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // 4. Crear el token de Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Devolver la respuesta exitosa para React
        return response()->json([
            'message' => '¡Bienvenido a GymHub!',
            'access_token' => $token,
            'user' => $user
        ]);
    }
}