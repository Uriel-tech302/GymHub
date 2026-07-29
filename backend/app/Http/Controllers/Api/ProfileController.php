<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfilePhotoRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado.
     */
    public function show(Request $request): UserResource
    {
        return new UserResource(
            $request->user()
        );
    }

    /**
     * Actualizar nombre, correo o teléfono.
     */
    public function update(
        UpdateProfileRequest $request
    ) {
        $usuario = $request->user();

        $usuario->update(
            $request->validated()
        );

        return (new UserResource($usuario->fresh()))
            ->additional([
                'message' => 'Perfil actualizado correctamente.',
            ]);
    }

    /**
     * Guardar o reemplazar la fotografía de perfil.
     */
    public function updatePhoto(
        UpdateProfilePhotoRequest $request
    ) {
        $usuario = $request->user();

        /*
         * Eliminar la fotografía anterior para no acumular
         * archivos innecesarios en el servidor.
         */
        if (
            $usuario->foto_perfil &&
            Storage::disk('public')->exists(
                $usuario->foto_perfil
            )
        ) {
            Storage::disk('public')->delete(
                $usuario->foto_perfil
            );
        }

        $ruta = $request
            ->file('foto_perfil')
            ->store('perfiles', 'public');

        $usuario->update([
            'foto_perfil' => $ruta,
        ]);

        return (new UserResource($usuario->fresh()))
            ->additional([
                'message' => 'Fotografía actualizada correctamente.',
            ]);
    }

    /**
     * Eliminar la fotografía actual.
     */
    public function deletePhoto(
        Request $request
    ): JsonResponse {
        $usuario = $request->user();

        if (!$usuario->foto_perfil) {
            return response()->json([
                'message' => 'El usuario no tiene una fotografía de perfil.',
            ], 404);
        }

        if (
            Storage::disk('public')->exists(
                $usuario->foto_perfil
            )
        ) {
            Storage::disk('public')->delete(
                $usuario->foto_perfil
            );
        }

        $usuario->update([
            'foto_perfil' => null,
        ]);

        return response()->json([
            'message' => 'Fotografía eliminada correctamente.',
        ], 200);
    }

    /**
     * Cambiar la contraseña del usuario autenticado.
     */
    public function updatePassword(
        UpdatePasswordRequest $request
    ): JsonResponse {
        $datos = $request->validated();
        $usuario = $request->user();

        if (
            !Hash::check(
                $datos['current_password'],
                $usuario->password
            )
        ) {
            throw ValidationException::withMessages([
                'current_password' => [
                    'La contraseña actual no es correcta.',
                ],
            ]);
        }

        $usuario->update([
            'password' => Hash::make(
                $datos['password']
            ),
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada correctamente.',
        ], 200);
    }
}