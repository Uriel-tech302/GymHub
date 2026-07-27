<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();

            // Relaciones (Foreign Keys)
            $table->foreignId('id_venta')->constrained('ventas');
            $table->foreignId('id_producto')->nullable()->constrained('productos');
            $table->foreignId('id_membresia')->nullable()->constrained('membresias');

            $table->integer('cantidad');
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};
