<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Membresia;

class MembresiaController extends Controller
{
    // 1. Mostrar todas las membresías (GET)
    public function index()
    {
        $membresias = Membresia::all();
        return response()->json($membresias, 200);
    }

    // 2. Crear una nueva membresía (POST)
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'duracion_dias' => 'required|integer',
        ]);

        $membresia = Membresia::create($request->all());

        return response()->json([
            'message' => 'Membresía creada con éxito',
            'data' => $membresia
        ], 201);
    }

    // 3. Mostrar una membresía en específico (GET)
    public function show($id)
    {
        $membresia = Membresia::find($id);
        if (!$membresia) {
            return response()->json(['message' => 'Membresía no encontrada'], 404);
        }
        return response()->json($membresia, 200);
    }

    // 4. Actualizar una membresía (PUT/PATCH)
    public function update(Request $request, $id)
    {
        $membresia = Membresia::find($id);
        if (!$membresia) {
            return response()->json(['message' => 'Membresía no encontrada'], 404);
        }

        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'precio' => 'sometimes|required|numeric',
            'duracion_dias' => 'sometimes|required|integer',
        ]);

        $membresia->update($request->all());

        return response()->json([
            'message' => 'Membresía actualizada con éxito',
            'data' => $membresia
        ], 200);
    }

    // 5. Eliminar una membresía (DELETE)
    public function destroy($id)
    {
        $membresia = Membresia::find($id);
        if (!$membresia) {
            return response()->json(['message' => 'Membresía no encontrada'], 404);
        }

        $membresia->delete();

        return response()->json(['message' => 'Membresía eliminada correctamente'], 200);
    }
}