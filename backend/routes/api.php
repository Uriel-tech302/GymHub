<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MembresiaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\RutinaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VentaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;


/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
|
| No requieren token de autenticación.
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas protegidas con Laravel Sanctum
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Autenticación
    |--------------------------------------------------------------------------
    */

    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
|--------------------------------------------------------------------------
| Perfil del usuario autenticado
|--------------------------------------------------------------------------
*/

    Route::prefix('profile')->group(function () {
        Route::get(
            '/',
            [ProfileController::class, 'show']
        );

        Route::patch(
            '/',
            [ProfileController::class, 'update']
        );

        Route::post(
            '/photo',
            [ProfileController::class, 'updatePhoto']
        );

        Route::delete(
            '/photo',
            [ProfileController::class, 'deletePhoto']
        );

        Route::patch(
            '/password',
            [ProfileController::class, 'updatePassword']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Ejercicios de Wger
    |--------------------------------------------------------------------------
    |
    | Los tres roles pueden consultar ejercicios.
    |
    */

    Route::middleware(
        'role:Administrador,Empleado,Cliente'
    )->group(function () {
        Route::get(
            '/ejercicios',
            [RutinaController::class, 'obtenerEjercicios']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Consulta de membresías
    |--------------------------------------------------------------------------
    |
    | Administrador: administra el catálogo.
    | Empleado: consulta planes para registrar ventas.
    | Cliente: consulta los planes disponibles.
    |
    */

    Route::middleware(
        'role:Administrador,Empleado,Cliente'
    )->group(function () {
        Route::get(
            '/membresias',
            [MembresiaController::class, 'index']
        );

        Route::get(
            '/membresias/{membresia}',
            [MembresiaController::class, 'show']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Consulta de productos
    |--------------------------------------------------------------------------
    |
    | El administrador y el empleado pueden consultar inventario.
    |
    */

    Route::middleware(
        'role:Administrador,Empleado'
    )->group(function () {
        Route::get(
            '/productos',
            [ProductoController::class, 'index']
        );

        Route::get(
            '/productos/{producto}',
            [ProductoController::class, 'show']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Ventas
    |--------------------------------------------------------------------------
    |
    | Administradores y empleados pueden registrar y consultar ventas.
    |
    */

    Route::middleware(
        'role:Administrador,Empleado'
    )->group(function () {
        Route::apiResource(
            'ventas',
            VentaController::class
        )->only([
            'index',
            'store',
            'show',
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Administración general
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Administrador')->group(function () {

        // CRUD completo de usuarios
        Route::apiResource(
            'users',
            UserController::class
        );

        // Crear, actualizar y eliminar membresías
        Route::apiResource(
            'membresias',
            MembresiaController::class
        )->except([
            'index',
            'show',
        ]);

        // Crear, actualizar y eliminar productos
        Route::apiResource(
            'productos',
            ProductoController::class
        )->except([
            'index',
            'show',
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Área del cliente
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Cliente')->group(function () {
        Route::get('/mi-progreso', function () {
            return response()->json([
                'message' => 'Acceso concedido al área del cliente.',
            ]);
        });
    });
});
