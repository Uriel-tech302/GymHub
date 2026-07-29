<?php

namespace Database\Seeders;

use App\Models\Membresia;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class VentaSeeder extends Seeder
{
    /**
     * Completa el historial hasta tener al menos 15 ventas.
     */
    public function run(): void
    {
        $ventasFaltantes = max(
            0,
            15 - Venta::count()
        );

        if ($ventasFaltantes === 0) {
            return;
        }

        $empleados = User::query()
            ->whereIn('role', [
                'Administrador',
                'Empleado',
            ])
            ->get();

        $clientes = User::query()
            ->where('role', 'Cliente')
            ->get();

        if (
            $empleados->isEmpty() ||
            $clientes->isEmpty()
        ) {
            throw new RuntimeException(
                'Primero deben existir empleados y clientes.'
            );
        }

        for (
            $indice = 0;
            $indice < $ventasFaltantes;
            $indice++
        ) {
            DB::transaction(
                function () use (
                    $indice,
                    $empleados,
                    $clientes
                ) {
                    $empleado = $empleados->random();

                    /*
                     * Cada tercera venta incluye una membresía.
                     */
                    $incluyeMembresia =
                        $indice % 3 === 0;

                    /*
                     * Algunas ventas de productos pueden no tener cliente.
                     */
                    $cliente = (
                        $incluyeMembresia ||
                        $indice % 2 === 0
                    )
                        ? $clientes->random()
                        : null;

                    $venta = Venta::create([
                        'fecha' => now()
                            ->subDays($indice)
                            ->subHours(
                                random_int(0, 10)
                            ),

                        'total' => 0,

                        'id_empleado' =>
                            $empleado->id,

                        'id_cliente' =>
                            $cliente?->id,
                    ]);

                    $total = 0;

                    /*
                     * Agregar entre uno y dos productos.
                     */
                    $productos = Producto::query()
                        ->where('stock', '>', 0)
                        ->inRandomOrder()
                        ->limit(random_int(1, 2))
                        ->lockForUpdate()
                        ->get();

                    if ($productos->isEmpty()) {
                        throw new RuntimeException(
                            'No existen productos con stock disponible.'
                        );
                    }

                    foreach ($productos as $producto) {
                        $cantidad = min(
                            random_int(1, 3),
                            $producto->stock
                        );

                        $subtotal = round(
                            (float) $producto->precio *
                            $cantidad,
                            2
                        );

                        $venta->detalles()->create([
                            'id_producto' =>
                                $producto->id,

                            'id_membresia' => null,

                            'cantidad' => $cantidad,

                            'subtotal' => $subtotal,
                        ]);

                        $producto->decrement(
                            'stock',
                            $cantidad
                        );

                        $total += $subtotal;
                    }

                    /*
                     * Agregar una membresía en algunas ventas.
                     */
                    if ($incluyeMembresia) {
                        $membresia = Membresia::query()
                            ->inRandomOrder()
                            ->first();

                        if (!$membresia || !$cliente) {
                            throw new RuntimeException(
                                'No se pudo asignar la membresía.'
                            );
                        }

                        $subtotalMembresia =
                            (float) $membresia->precio;

                        $venta->detalles()->create([
                            'id_producto' => null,

                            'id_membresia' =>
                                $membresia->id,

                            'cantidad' => 1,

                            'subtotal' =>
                                $subtotalMembresia,
                        ]);

                        $total += $subtotalMembresia;

                        $hoy = Carbon::today();

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
                                    ->addDays(
                                        $membresia
                                            ->duracion_dias
                                    )
                                    ->toDateString(),
                        ]);
                    }

                    $venta->update([
                        'total' => round($total, 2),
                    ]);
                }
            );
        }
    }
}