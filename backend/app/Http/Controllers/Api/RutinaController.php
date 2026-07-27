<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class RutinaController extends Controller
{
    public function obtenerEjercicios()
    {
        try {
            // Hacemos una petición GET a la API pública de Wger
            // Limitamos a 10 resultados y pedimos que estén en español (language=2) si es posible
            // Código actualizado
            $response = Http::withoutVerifying()->get('https://wger.de/api/v2/exercise/', [
                'limit' => 10,
                'language' => 2 
            ]);

            // Si la API responde correctamente, devolvemos sus datos
            if ($response->successful()) {
                return response()->json([
                    'message' => 'Ejercicios obtenidos exitosamente',
                    'data' => $response->json()['results']
                ], 200);
            }

            return response()->json(['message' => 'No se pudieron obtener los ejercicios en este momento'], $response->status());

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error de conexión con Wger', 'error' => $e->getMessage()], 500);
        }
    }
}