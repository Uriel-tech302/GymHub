<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Crea usuarios fijos para los tres roles del sistema.
     */
    public function run(): void
    {
        $password = Hash::make('GymHub2026*');

        $usuarios = [
            [
                'name' => 'Administrador Principal',
                'email' => 'admin@gymhub.com',
                'telefono' => '+529511000001',
                'role' => 'Administrador',
                'fecha_vencimiento' => null,
            ],
            [
                'name' => 'Developer GymHub',
                'email' => 'developer@gymhub.com',
                'telefono' => '+529511000002',
                'role' => 'Administrador',
                'fecha_vencimiento' => null,
            ],
            [
                'name' => 'Empleado Principal',
                'email' => 'empleado@gymhub.com',
                'telefono' => '+529511000003',
                'role' => 'Empleado',
                'fecha_vencimiento' => null,
            ],
            [
                'name' => 'María López Hernández',
                'email' => 'maria.empleado@gymhub.com',
                'telefono' => '+529511000004',
                'role' => 'Empleado',
                'fecha_vencimiento' => null,
            ],
            [
                'name' => 'Carlos Martínez Ruiz',
                'email' => 'carlos.empleado@gymhub.com',
                'telefono' => '+529511000005',
                'role' => 'Empleado',
                'fecha_vencimiento' => null,
            ],
            [
                'name' => 'Cliente Principal',
                'email' => 'cliente@gymhub.com',
                'telefono' => '+529511000006',
                'role' => 'Cliente',
                'fecha_vencimiento' => Carbon::today()
                    ->addDays(20)
                    ->toDateString(),
            ],
            [
                'name' => 'Ana García Pérez',
                'email' => 'ana.garcia@gymhub.com',
                'telefono' => '+529511000007',
                'role' => 'Cliente',
                'fecha_vencimiento' => Carbon::today()
                    ->addDays(10)
                    ->toDateString(),
            ],
            [
                'name' => 'José Hernández Cruz',
                'email' => 'jose.hernandez@gymhub.com',
                'telefono' => '+529511000008',
                'role' => 'Cliente',
                'fecha_vencimiento' => Carbon::today()
                    ->addDays(3)
                    ->toDateString(),
            ],
            [
                'name' => 'Fernanda Ramírez Díaz',
                'email' => 'fernanda.ramirez@gymhub.com',
                'telefono' => '+529511000009',
                'role' => 'Cliente',
                'fecha_vencimiento' => Carbon::today()
                    ->addDays(45)
                    ->toDateString(),
            ],
            [
                'name' => 'Miguel Ángel Torres',
                'email' => 'miguel.torres@gymhub.com',
                'telefono' => '+529511000010',
                'role' => 'Cliente',
                'fecha_vencimiento' => Carbon::today()
                    ->subDays(5)
                    ->toDateString(),
            ],
            [
                'name' => 'Sofía Morales Sánchez',
                'email' => 'sofia.morales@gymhub.com',
                'telefono' => '+529511000011',
                'role' => 'Cliente',
                'fecha_vencimiento' => null,
            ],
            [
                'name' => 'Luis Alberto Jiménez',
                'email' => 'luis.jimenez@gymhub.com',
                'telefono' => '+529511000012',
                'role' => 'Cliente',
                'fecha_vencimiento' => Carbon::today()
                    ->addDays(60)
                    ->toDateString(),
            ],
            [
                'name' => 'Diana Flores Reyes',
                'email' => 'diana.flores@gymhub.com',
                'telefono' => '+529511000013',
                'role' => 'Cliente',
                'fecha_vencimiento' => Carbon::today()
                    ->addDays(15)
                    ->toDateString(),
            ],
            [
                'name' => 'Pedro Antonio Méndez',
                'email' => 'pedro.mendez@gymhub.com',
                'telefono' => '+529511000014',
                'role' => 'Cliente',
                'fecha_vencimiento' => null,
            ],
            [
                'name' => 'Valeria Sánchez Ortiz',
                'email' => 'valeria.sanchez@gymhub.com',
                'telefono' => '+529511000015',
                'role' => 'Cliente',
                'fecha_vencimiento' => Carbon::today()
                    ->addDays(25)
                    ->toDateString(),
            ],
        ];

        foreach ($usuarios as $datosUsuario) {
            User::updateOrCreate(
                [
                    'email' => $datosUsuario['email'],
                ],
                [
                    ...$datosUsuario,
                    'password' => $password,
                    'foto_perfil' => null,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}