<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Obtener usuarios con paginación real del servidor y filtro
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro enviado desde React (?search=...)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Paginación de 10 registros por página
        return response()->json($query->paginate(10));
    }

    /**
     * Eliminar un usuario por ID
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
}