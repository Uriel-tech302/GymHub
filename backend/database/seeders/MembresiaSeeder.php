<?php

namespace Database\Seeders;

use App\Models\Membresia;
use Illuminate\Database\Seeder;

class MembresiaSeeder extends Seeder
{
    /**
     * Crea el catálogo de planes disponibles.
     */
    public function run(): void
    {
        $membresias = [
            [
                'nombre' => 'Visita individual',
                'precio' => 50.00,
                'duracion_dias' => 1,
            ],
            [
                'nombre' => 'Plan semanal',
                'precio' => 150.00,
                'duracion_dias' => 7,
            ],
            [
                'nombre' => 'Plan quincenal',
                'precio' => 260.00,
                'duracion_dias' => 15,
            ],
            [
                'nombre' => 'Plan mensual',
                'precio' => 450.00,
                'duracion_dias' => 30,
            ],
            [
                'nombre' => 'Plan mensual estudiante',
                'precio' => 380.00,
                'duracion_dias' => 30,
            ],
            [
                'nombre' => 'Plan bimestral',
                'precio' => 820.00,
                'duracion_dias' => 60,
            ],
            [
                'nombre' => 'Plan trimestral',
                'precio' => 1150.00,
                'duracion_dias' => 90,
            ],
            [
                'nombre' => 'Plan semestral',
                'precio' => 2100.00,
                'duracion_dias' => 180,
            ],
            [
                'nombre' => 'Plan anual',
                'precio' => 3900.00,
                'duracion_dias' => 365,
            ],
            [
                'nombre' => 'Plan pareja mensual',
                'precio' => 800.00,
                'duracion_dias' => 30,
            ],
        ];

        foreach ($membresias as $membresia) {
            Membresia::updateOrCreate(
                [
                    'nombre' => $membresia['nombre'],
                ],
                $membresia
            );
        }
    }
}