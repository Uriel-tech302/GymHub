<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear tu usuario Administrador oficial
        User::factory()->create([
            'name' => 'Admin Principal',
            'email' => 'admin@gymhub.com',
            'password' => Hash::make('12345678'), // Contraseña fácil para pruebas
            'role' => 'Administrador',
        ]);

        // 2. Crear los 15 usuarios de prueba (Clientes y Empleados)
        User::factory(15)->create();
    }
}