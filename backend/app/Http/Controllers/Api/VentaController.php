<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index()
    {
        // Traemos las ventas INCLUYENDO sus detalles relacionados
        $ventas = Venta::with('detalles')->get();
        return response()->json($ventas, 200);
    }

    public function store(Request $request)
    {
        // 1. Validamos que nos envíen los datos de la venta y un arreglo de detalles
        $request->validate([
            'id_empleado' => 'required|integer',
            'id_cliente' => 'nullable|integer',
            'detalles' => 'required|array', // Aquí vendrán los productos/membresías
            'detalles.*.id_producto' => 'nullable|integer',
            'detalles.*.id_membresia' => 'nullable|integer',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.subtotal' => 'required|numeric|min:0',
        ]);

        // 2. Iniciamos una Transacción para proteger los datos
        DB::beginTransaction();

        try {
            // Calculamos el total sumando los subtotales de los detalles
            $totalVenta = collect($request->detalles)->sum('subtotal');

            // 3. Creamos el registro en la tabla VENTAS
            $venta = Venta::create([
                'fecha' => Carbon::now(),
                'total' => $totalVenta,
                'id_empleado' => $request->id_empleado,
                'id_cliente' => $request->id_cliente,
            ]);

            // 4. Recorremos el arreglo de detalles y los guardamos en VENTA_DETALLE
            foreach ($request->detalles as $detalle) {
                VentaDetalle::create([
                    'id_venta' => $venta->id,
                    'id_producto' => $detalle['id_producto'] ?? null,
                    'id_membresia' => $detalle['id_membresia'] ?? null,
                    'cantidad' => $detalle['cantidad'],
                    'subtotal' => $detalle['subtotal'],
                ]);
            }

            // Si todo salió bien, confirmamos los cambios en la base de datos
            DB::commit();

            return response()->json([
                'message' => 'Venta registrada con éxito',
                'venta_id' => $venta->id
            ], 201);
        } catch (\Exception $e) {
            // Si hubo algún error, deshacemos todo para no dejar datos corruptos
            DB::rollBack();
            return response()->json(['message' => 'Error al registrar la venta', 'error' => $e->getMessage()], 500);
        }
    }
}
