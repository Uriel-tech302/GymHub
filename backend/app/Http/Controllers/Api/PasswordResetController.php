<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Solicitar un enlace para restablecer la contraseña.
     */
    public function forgotPassword(
        ForgotPasswordRequest $request
    ): JsonResponse {
        $estado = Password::sendResetLink(
            $request->only('email')
        );

        /*
         * Impide solicitar demasiados enlaces
         * en un periodo corto.
         */
        if ($estado === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Espera un momento antes de solicitar otro enlace.',
            ], 429);
        }

        /*
         * Se responde igual aunque el correo no exista.
         * Así no revelamos qué cuentas están registradas.
         */
        return response()->json([
            'message' => 'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.',
        ], 200);
    }

    /**
     * Establecer la contraseña nueva mediante un token.
     */
    public function resetPassword(
        ResetPasswordRequest $request
    ): JsonResponse {
        $estado = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),

            function (
                User $usuario,
                string $password
            ): void {
                $usuario
                    ->forceFill([
                        'password' => Hash::make($password),
                    ])
                    ->setRememberToken(
                        Str::random(60)
                    );

                $usuario->save();

                // Cerrar las sesiones anteriores de Sanctum.
                $usuario->tokens()->delete();

                event(
                    new PasswordReset($usuario)
                );
            }
        );

        if ($estado === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Contraseña restablecida correctamente. Ya puedes iniciar sesión.',
            ], 200);
        }

        return response()->json([
            'message' => 'El enlace es inválido, ya fue utilizado o ha expirado.',
            'errors' => [
                'token' => [
                    'El token de recuperación no es válido.',
                ],
            ],
        ], 422);
    }
}
