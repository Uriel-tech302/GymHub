<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Productos\StoreProductoRequest;
use App\Http\Requests\Productos\UpdateProductoRequest;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar productos con paginación, búsqueda y filtros.
     *
     * Ejemplos:
     * GET /api/productos?page=1&per_page=10
     * GET /api/productos?search=proteina
     * GET /api/productos?estado_stock=agotado
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query(
            'per_page',
            10
        );

        // Se permiten entre 1 y 50 registros por página.
        $perPage = min(
            max($perPage, 1),
            50
        );

        $search = trim(
            (string) $request->query('search', '')
        );

        $estadoStock = strtolower(
            trim(
                (string) $request->query(
                    'estado_stock',
                    ''
                )
            )
        );

        $productos = Producto::query()

            /*
             * Buscar coincidencias en el nombre.
             */
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

            /*
             * Filtrar productos agotados, con stock bajo
             * o con disponibilidad normal.
             */
            ->when(
                $estadoStock === 'agotado',
                function ($query) {
                    $query->where('stock', 0);
                }
            )

            ->when(
                $estadoStock === 'bajo',
                function ($query) {
                    $query->whereBetween(
                        'stock',
                        [1, 5]
                    );
                }
            )

            ->when(
                $estadoStock === 'disponible',
                function ($query) {
                    $query->where('stock', '>', 5);
                }
            )

            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return ProductoResource::collection(
            $productos
        );
    }

    /**
     * Registrar un producto.
     */
    public function store(
        StoreProductoRequest $request
    ) {
        $producto = Producto::create(
            $request->validated()
        );

        return (new ProductoResource($producto))
            ->additional([
                'message' => 'Producto registrado correctamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar un producto específico.
     *
     * Laravel devolverá 404 automáticamente si no existe.
     */
    public function show(
        Producto $producto
    ): ProductoResource {
        return new ProductoResource($producto);
    }

    /**
     * Actualizar un producto.
     */
    public function update(
        UpdateProductoRequest $request,
        Producto $producto
    ) {
        $producto->update(
            $request->validated()
        );

        return (new ProductoResource(
            $producto->fresh()
        ))->additional([
            'message' => 'Producto actualizado correctamente.',
        ]);
    }

    /**
     * Eliminar un producto cuando no tenga ventas relacionadas.
     */
    public function destroy(
        Producto $producto
    ): JsonResponse {
        $tieneVentas = VentaDetalle::query()
            ->where(
                'id_producto',
                $producto->id
            )
            ->exists();

        if ($tieneVentas) {
            return response()->json([
                'message' => 'No se puede eliminar el producto porque forma parte del historial de ventas.',
            ], 409);
        }

        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente.',
        ], 200);
    }
}