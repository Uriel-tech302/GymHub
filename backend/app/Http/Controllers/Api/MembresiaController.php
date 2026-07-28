<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Membresias\IndexMembresiaRequest;
use App\Http\Requests\Membresias\StoreMembresiaRequest;
use App\Http\Requests\Membresias\UpdateMembresiaRequest;
use App\Http\Resources\MembresiaResource;
use App\Models\Membresia;
use App\Models\VentaDetalle;
use Illuminate\Http\JsonResponse;

class MembresiaController extends Controller
{
    /**
     * Listar membresías con paginación y filtros.
     *
     * Ejemplo:
     * GET /api/membresias?search=mensual&per_page=5
     */
    public function index(
        IndexMembresiaRequest $request
    ) {
        $datos = $request->validated();

        $perPage = (int) (
            $datos['per_page'] ?? 10
        );

        $search = trim(
            (string) ($datos['search'] ?? '')
        );

        $membresias = Membresia::query()

            // Buscar por nombre.
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        'nombre',
                        'like',
                        "%{$search}%"
                    );
                }
            )

            // Filtrar por precio mínimo.
            ->when(
                isset($datos['precio_min']),
                function ($query) use ($datos) {
                    $query->where(
                        'precio',
                        '>=',
                        $datos['precio_min']
                    );
                }
            )

            // Filtrar por precio máximo.
            ->when(
                isset($datos['precio_max']),
                function ($query) use ($datos) {
                    $query->where(
                        'precio',
                        '<=',
                        $datos['precio_max']
                    );
                }
            )

            // Filtrar por duración mínima.
            ->when(
                isset($datos['duracion_min']),
                function ($query) use ($datos) {
                    $query->where(
                        'duracion_dias',
                        '>=',
                        $datos['duracion_min']
                    );
                }
            )

            // Filtrar por duración máxima.
            ->when(
                isset($datos['duracion_max']),
                function ($query) use ($datos) {
                    $query->where(
                        'duracion_dias',
                        '<=',
                        $datos['duracion_max']
                    );
                }
            )

            ->orderBy('precio')
            ->paginate($perPage)
            ->withQueryString();

        return MembresiaResource::collection(
            $membresias
        );
    }

    /**
     * Registrar una membresía.
     */
    public function store(
        StoreMembresiaRequest $request
    ) {
        $membresia = Membresia::create(
            $request->validated()
        );

        return (new MembresiaResource($membresia))
            ->additional([
                'message' => 'Membresía registrada correctamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Consultar una membresía específica.
     */
    public function show(
        Membresia $membresia
    ): MembresiaResource {
        return new MembresiaResource(
            $membresia
        );
    }

    /**
     * Actualizar una membresía.
     */
    public function update(
        UpdateMembresiaRequest $request,
        Membresia $membresia
    ) {
        $membresia->update(
            $request->validated()
        );

        return (new MembresiaResource(
            $membresia->fresh()
        ))->additional([
            'message' => 'Membresía actualizada correctamente.',
        ]);
    }

    /**
     * Eliminar una membresía únicamente cuando
     * no forme parte del historial de ventas.
     */
    public function destroy(
        Membresia $membresia
    ): JsonResponse {
        $tieneVentas = VentaDetalle::query()
            ->where(
                'id_membresia',
                $membresia->id
            )
            ->exists();

        if ($tieneVentas) {
            return response()->json([
                'message' => 'No se puede eliminar la membresía porque forma parte del historial de ventas.',
            ], 409);
        }

        $membresia->delete();

        return response()->json([
            'message' => 'Membresía eliminada correctamente.',
        ], 200);
    }
}