<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Solamente se permiten imágenes de hasta 2 MB.
     */
    public function rules(): array
    {
        return [
            'foto_perfil' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'foto_perfil.required' => 'Debes seleccionar una fotografía.',
            'foto_perfil.image' => 'El archivo seleccionado debe ser una imagen.',
            'foto_perfil.mimes' => 'La fotografía debe estar en formato JPG, JPEG, PNG o WEBP.',
            'foto_perfil.max' => 'La fotografía no puede superar los 2 MB.',
        ];
    }
}