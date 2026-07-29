<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class RutinaController extends Controller
{
    /**
     * Obtiene ejercicios completos desde la API pública de Wger.
     */
    public function obtenerEjercicios(): JsonResponse
    {
        try {
            /*
             * Creamos el cliente HTTP con límites de espera
             * para evitar que GymHub quede bloqueado.
             */
            $cliente = Http::acceptJson()
                ->connectTimeout(8)
                ->timeout(20)
                ->retry(
                    times: 2,
                    sleepMilliseconds: 500,
                    throw: false
                );

            /*
             * Solución temporal únicamente para Windows local.
             *
             * En producción no se desactiva la verificación SSL.
             */
            if (app()->environment('local')) {
                $cliente = $cliente->withoutVerifying();
            }

            /*
             * exerciseinfo incluye traducciones, imágenes,
             * músculos, categorías y equipo.
             */
            $respuesta = $cliente->get(
                'https://wger.de/api/v2/exerciseinfo/',
                [
                    'limit' => 20,
                ]
            );

            if (!$respuesta->successful()) {
                return response()->json([
                    'message' => 'El servicio de ejercicios no está disponible en este momento.',
                ], 503);
            }

            $contenido = $respuesta->json();

            return response()->json([
                'message' => 'Ejercicios obtenidos correctamente.',
                'data' => $contenido['results'] ?? [],
            ], 200);

        } catch (ConnectionException $exception) {
            report($exception);

            return response()->json([
                'message' => 'No fue posible conectar con el servicio de ejercicios.',
            ], 503);

        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Ocurrió un error al consultar los ejercicios.',
            ], 500);
        }
    }
}