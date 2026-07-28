<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ventas\IndexVentaRequest;
use App\Http\Requests\Ventas\StoreVentaRequest;
use App\Http\Resources\VentaResource;
use App\Models\Membresia;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class VentaController extends Controller
{
    /**
     * Mostrar el historial de ventas con paginación y filtros.
     *
     * Administrador:
     * - Puede consultar todas las ventas.
     *
     * Empleado:
     * - Solamente puede consultar las ventas que registró.
     */
    public function index(
        IndexVentaRequest $request
    ) {
        $datos = $request->validated();
        $usuarioAutenticado = $request->user();

        $perPage = (int) (
            $datos['per_page'] ?? 10
        );

        $search = trim(
            (string) ($datos['search'] ?? '')
        );

        $consulta = Venta::query()
            ->with([
                'empleado:id,name,email,role',

                'cliente:id,name,email,telefono,fecha_vencimiento',

                'detalles.producto:id,nombre,precio,stock',

                'detalles.membresia:id,nombre,precio,duracion_dias',
            ]);

        /*
         * Un empleado solamente puede consultar
         * las ventas que él mismo registró.
         */
        if ($usuarioAutenticado->role === 'Empleado') {
            $consulta->where(
                'id_empleado',
                $usuarioAutenticado->id
            );
        } elseif (isset($datos['id_empleado'])) {
            /*
             * El administrador sí puede filtrar
             * por cualquier empleado.
             */
            $consulta->where(
                'id_empleado',
                $datos['id_empleado']
            );
        }

        /*
         * Filtrar por cliente.
         */
        $consulta->when(
            isset($datos['id_cliente']),
            function ($query) use ($datos) {
                $query->where(
                    'id_cliente',
                    $datos['id_cliente']
                );
            }
        );

        /*
         * Buscar por nombre o correo del cliente
         * y del empleado responsable.
         */
        $consulta->when(
            $search !== '',
            function ($query) use ($search) {
                $query->where(function ($subconsulta) use ($search) {
                    $subconsulta
                        ->whereHas(
                            'cliente',
                            function ($cliente) use ($search) {
                                $cliente
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        )
                        ->orWhereHas(
                            'empleado',
                            function ($empleado) use ($search) {
                                $empleado
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                });
            }
        );

        /*
         * Filtros por fecha.
         */
        $consulta->when(
            isset($datos['fecha_desde']),
            function ($query) use ($datos) {
                $query->whereDate(
                    'fecha',
                    '>=',
                    $datos['fecha_desde']
                );
            }
        );

        $consulta->when(
            isset($datos['fecha_hasta']),
            function ($query) use ($datos) {
                $query->whereDate(
                    'fecha',
                    '<=',
                    $datos['fecha_hasta']
                );
            }
        );

        /*
         * Filtros por total.
         */
        $consulta->when(
            isset($datos['total_min']),
            function ($query) use ($datos) {
                $query->where(
                    'total',
                    '>=',
                    $datos['total_min']
                );
            }
        );

        $consulta->when(
            isset($datos['total_max']),
            function ($query) use ($datos) {
                $query->where(
                    'total',
                    '<=',
                    $datos['total_max']
                );
            }
        );

        $ventas = $consulta
            ->orderByDesc('fecha')
            ->paginate($perPage)
            ->withQueryString();

        return VentaResource::collection($ventas);
    }

    /**
     * Mostrar una venta individual.
     */
    public function show(
        Request $request,
        Venta $venta
    ) {
        $usuarioAutenticado = $request->user();

        /*
         * Un empleado no puede abrir el ticket
         * registrado por otro empleado.
         */
        if (
            $usuarioAutenticado->role === 'Empleado' &&
            $venta->id_empleado !== $usuarioAutenticado->id
        ) {
            return response()->json([
                'message' => 'No tienes permiso para consultar esta venta.',
            ], 403);
        }

        $venta->load([
            'empleado:id,name,email,role',

            'cliente:id,name,email,telefono,fecha_vencimiento',

            'detalles.producto:id,nombre,precio,stock',

            'detalles.membresia:id,nombre,precio,duracion_dias',
        ]);

        return new VentaResource($venta);
    }

    /**
     * Registrar una venta transaccional.
     */
    public function store(
        StoreVentaRequest $request
    ) {
        $datos = $request->validated();

        try {
            /*
             * Si ocurre un error dentro de esta función,
             * Laravel revierte automáticamente:
             *
             * - La venta.
             * - Los detalles.
             * - El stock descontado.
             * - La fecha de vencimiento.
             */
            $venta = DB::transaction(
                function () use ($request, $datos) {
                    $empleado = $request->user();
                    $cliente = null;

                    /*
                     * Buscar y bloquear al cliente cuando fue enviado.
                     */
                    if (!empty($datos['id_cliente'])) {
                        $cliente = User::query()
                            ->whereKey($datos['id_cliente'])
                            ->lockForUpdate()
                            ->first();

                        if (!$cliente) {
                            throw ValidationException::withMessages([
                                'id_cliente' => [
                                    'El cliente seleccionado ya no se encuentra disponible.',
                                ],
                            ]);
                        }

                        if ($cliente->role !== 'Cliente') {
                            throw ValidationException::withMessages([
                                'id_cliente' => [
                                    'El usuario seleccionado no tiene el rol Cliente.',
                                ],
                            ]);
                        }
                    }

                    $detallesPreparados = [];
                    $totalCentavos = 0;
                    $diasMembresia = 0;

                    foreach (
                        $datos['detalles']
                        as $indice => $detalle
                    ) {
                        $cantidad = (int) $detalle['cantidad'];

                        /*
                         * Procesar un producto.
                         */
                        if (!empty($detalle['id_producto'])) {
                            $producto = Producto::query()
                                ->whereKey(
                                    $detalle['id_producto']
                                )
                                ->lockForUpdate()
                                ->first();

                            if (!$producto) {
                                throw ValidationException::withMessages([
                                    "detalles.{$indice}.id_producto" => [
                                        'El producto seleccionado ya no se encuentra disponible.',
                                    ],
                                ]);
                            }

                            if ($producto->stock < $cantidad) {
                                throw ValidationException::withMessages([
                                    "detalles.{$indice}.cantidad" => [
                                        "Stock insuficiente para {$producto->nombre}. Disponibles: {$producto->stock}.",
                                    ],
                                ]);
                            }

                            /*
                             * Trabajamos en centavos para evitar
                             * errores de redondeo con valores decimales.
                             */
                            $precioCentavos = (int) round(
                                (float) $producto->precio * 100
                            );

                            $subtotalCentavos =
                                $precioCentavos * $cantidad;

                            $totalCentavos += $subtotalCentavos;

                            $detallesPreparados[] = [
                                'id_producto' => $producto->id,
                                'id_membresia' => null,
                                'cantidad' => $cantidad,

                                'subtotal' => number_format(
                                    $subtotalCentavos / 100,
                                    2,
                                    '.',
                                    ''
                                ),
                            ];

                            /*
                             * Descontar existencias.
                             */
                            $producto->decrement(
                                'stock',
                                $cantidad
                            );

                            continue;
                        }

                        /*
                         * Procesar una membresía.
                         */
                        $membresia = Membresia::query()
                            ->whereKey(
                                $detalle['id_membresia']
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$membresia) {
                            throw ValidationException::withMessages([
                                "detalles.{$indice}.id_membresia" => [
                                    'La membresía seleccionada ya no se encuentra disponible.',
                                ],
                            ]);
                        }

                        if (!$cliente) {
                            throw ValidationException::withMessages([
                                'id_cliente' => [
                                    'Debes seleccionar un cliente para vender una membresía.',
                                ],
                            ]);
                        }

                        $precioCentavos = (int) round(
                            (float) $membresia->precio * 100
                        );

                        $subtotalCentavos =
                            $precioCentavos * $cantidad;

                        $totalCentavos += $subtotalCentavos;

                        /*
                         * Permite comprar varios periodos.
                         *
                         * Ejemplo:
                         * Membresía de 30 días x cantidad 2
                         * = 60 días adicionales.
                         */
                        $diasMembresia +=
                            $membresia->duracion_dias *
                            $cantidad;

                        $detallesPreparados[] = [
                            'id_producto' => null,
                            'id_membresia' => $membresia->id,
                            'cantidad' => $cantidad,

                            'subtotal' => number_format(
                                $subtotalCentavos / 100,
                                2,
                                '.',
                                ''
                            ),
                        ];
                    }

                    /*
                     * La columna total es decimal(10,2).
                     * Impedimos superar su capacidad.
                     */
                    if ($totalCentavos > 9_999_999_999) {
                        throw ValidationException::withMessages([
                            'detalles' => [
                                'El total de la venta supera el máximo permitido.',
                            ],
                        ]);
                    }

                    /*
                     * Registrar la cabecera de la venta.
                     *
                     * El empleado se obtiene del token:
                     * nunca se recibe desde React.
                     */
                    $venta = Venta::create([
                        'fecha' => now(),

                        'total' => number_format(
                            $totalCentavos / 100,
                            2,
                            '.',
                            ''
                        ),

                        'id_empleado' => $empleado->id,
                        'id_cliente' => $cliente?->id,
                    ]);

                    /*
                     * Registrar todos los detalles.
                     */
                    $venta
                        ->detalles()
                        ->createMany($detallesPreparados);

                    /*
                     * Activar o ampliar la membresía del cliente.
                     */
                    if (
                        $cliente &&
                        $diasMembresia > 0
                    ) {
                        $hoy = Carbon::today();

                        /*
                         * Si todavía tiene una membresía activa,
                         * los nuevos días se agregan a su vencimiento.
                         *
                         * Si está vencida o no tiene fecha,
                         * los días comienzan a contar desde hoy.
                         */
                        $fechaBase =
                            $cliente->fecha_vencimiento &&
                            $cliente->fecha_vencimiento
                                ->greaterThanOrEqualTo($hoy)

                                ? $cliente
                                    ->fecha_vencimiento
                                    ->copy()

                                : $hoy->copy();

                        $cliente->update([
                            'fecha_vencimiento' =>
                                $fechaBase
                                    ->addDays($diasMembresia)
                                    ->toDateString(),
                        ]);
                    }

                    /*
                     * Regresar la venta con toda su información.
                     */
                    return $venta->load([
                        'empleado:id,name,email,role',

                        'cliente:id,name,email,telefono,fecha_vencimiento',

                        'detalles.producto:id,nombre,precio,stock',

                        'detalles.membresia:id,nombre,precio,duracion_dias',
                    ]);
                },

                /*
                 * Laravel reintentará hasta 3 veces
                 * si ocurre un bloqueo mutuo de MySQL.
                 */
                3
            );

            return (new VentaResource($venta))
                ->additional([
                    'message' => 'Venta registrada correctamente.',
                ])
                ->response()
                ->setStatusCode(201);

        } catch (ValidationException $exception) {
            /*
             * Conserva los errores de validación y su código 422.
             */
            throw $exception;

        } catch (Throwable $exception) {
            /*
             * Registra internamente el error real,
             * pero no expone detalles sensibles al frontend.
             */
            report($exception);

            return response()->json([
                'message' => 'No fue posible registrar la venta. Inténtalo nuevamente.',
            ], 500);
        }
    }
}