<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta todos los seeders en el orden correcto.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductoSeeder::class,
            MembresiaSeeder::class,
            VentaSeeder::class,
        ]);
    }
}