<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Crea productos realistas para el punto de venta.
     */
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Agua natural 1 litro',
                'precio' => 20.00,
                'stock' => 80,
            ],
            [
                'nombre' => 'Bebida energética 473 ml',
                'precio' => 45.00,
                'stock' => 45,
            ],
            [
                'nombre' => 'Proteína Whey 1 kg',
                'precio' => 649.90,
                'stock' => 25,
            ],
            [
                'nombre' => 'Creatina monohidratada 300 g',
                'precio' => 399.90,
                'stock' => 30,
            ],
            [
                'nombre' => 'Barra de proteína chocolate',
                'precio' => 38.00,
                'stock' => 70,
            ],
            [
                'nombre' => 'Barra de proteína vainilla',
                'precio' => 38.00,
                'stock' => 65,
            ],
            [
                'nombre' => 'Bebida isotónica 600 ml',
                'precio' => 32.00,
                'stock' => 50,
            ],
            [
                'nombre' => 'Toalla deportiva GymHub',
                'precio' => 120.00,
                'stock' => 20,
            ],
            [
                'nombre' => 'Shaker GymHub 700 ml',
                'precio' => 150.00,
                'stock' => 25,
            ],
            [
                'nombre' => 'Guantes para gimnasio',
                'precio' => 280.00,
                'stock' => 18,
            ],
            [
                'nombre' => 'Cinturón para levantamiento',
                'precio' => 450.00,
                'stock' => 12,
            ],
            [
                'nombre' => 'Banda elástica resistencia ligera',
                'precio' => 90.00,
                'stock' => 35,
            ],
            [
                'nombre' => 'Banda elástica resistencia fuerte',
                'precio' => 130.00,
                'stock' => 30,
            ],
            [
                'nombre' => 'Preentreno 30 servicios',
                'precio' => 520.00,
                'stock' => 16,
            ],
            [
                'nombre' => 'Aminoácidos BCAA 300 g',
                'precio' => 430.00,
                'stock' => 15,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(
                [
                    'nombre' => $producto['nombre'],
                ],
                $producto
            );
        }
    }
}
