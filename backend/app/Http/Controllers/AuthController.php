<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validamos que nos envíen correo y contraseña
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Verificamos las credenciales
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas. Verifica tu correo y contraseña.'
            ], 401);
        }

        // 3. Obtenemos al usuario (que será el gerente o dueño)
        $user = User::where('email', $request->email)->firstOrFail();
        
        // 4. Generamos el token con Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Devolvemos el token y los datos del usuario
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }
    public function logout(Request $request)
    {
        // Busca el token que está usando el usuario en este momento y lo elimina
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente. ¡Hasta luego!'
        ], 200);
    }
}