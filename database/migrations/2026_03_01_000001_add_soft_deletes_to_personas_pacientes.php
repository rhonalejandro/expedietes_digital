<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega soft deletes (deleted_at) a las tablas personas y pacientes.
 * El destroy del módulo de pacientes usará SoftDeletes en lugar de
 * eliminar físicamente los registros.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
