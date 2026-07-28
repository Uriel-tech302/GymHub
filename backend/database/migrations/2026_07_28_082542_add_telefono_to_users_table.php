<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el teléfono necesario para enviar
     * notificaciones por SMS y WhatsApp.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table
                ->string('telefono', 20)
                ->nullable()
                ->after('email');
        });
    }

    /**
     * Elimina el campo en caso de revertir la migración.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telefono');
        });
    }
};