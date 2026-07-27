<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// 1. Ruta pública para iniciar sesión
Route::post('login', [AuthController::class, 'login']);


// 2. RUTAS PROTEGIDAS (Requieren Token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    // --- ACCESOS DE ADMINISTRADOR ---
    // --- ACCESOS DE ADMINISTRADOR ---
    Route::middleware('role:Administrador')->group(function () {

        // Gestión de usuarios
        Route::apiResource('users', UserController::class);

        // Gestión de membresías (NUEVA)
        Route::apiResource('membresias', \App\Http\Controllers\Api\MembresiaController::class);
        // Gestión de inventario físico (NUEVA)
        Route::apiResource('productos', \App\Http\Controllers\Api\ProductoController::class);
        // Consultar ejercicios desde la API externa de Wger
        Route::get('/ejercicios', [\App\Http\Controllers\Api\RutinaController::class, 'obtenerEjercicios']);
    });

    // --- ACCESOS DE EMPLEADO (Recepcionistas, Entrenadores) ---
    Route::middleware('role:Empleado')->group(function () {
        // Rutas de prueba para el empleado
        Route::get('/panel-empleado', function () {
            return response()->json(['message' => 'Acceso concedido al área de empleados.']);
        });
        // Más adelante puedes agregar: registrar asistencias, ver inventario, etc.
        // Registrar ventas (NUEVA)
        Route::apiResource('ventas', \App\Http\Controllers\Api\VentaController::class);
        // Consultar ejercicios desde la API externa de Wger
        Route::get('/ejercicios', [\App\Http\Controllers\Api\RutinaController::class, 'obtenerEjercicios']);
    });

    // --- ACCESOS DE CLIENTE (Miembros del gimnasio) ---
    Route::middleware('role:Cliente')->group(function () {
        // Rutas de prueba para el cliente
        Route::get('/mi-progreso', function () {
            return response()->json(['message' => 'Acceso concedido. Aquí verás tus rutinas.']);
            // Consultar ejercicios desde la API externa de Wger
        Route::get('/ejercicios', [\App\Http\Controllers\Api\RutinaController::class, 'obtenerEjercicios']);
        });
        // Más adelante puedes agregar: ver membresía activa, rutinas asignadas, etc.
    });
});
