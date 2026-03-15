<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla modulos — Registro dinámico de módulos del sistema.
 *
 * El campo `slug` es la clave de unión con `permisos.modulo`.
 * No se usa FK entre ambas tablas para mantener compatibilidad
 * con permisos creados antes de que existiera esta tabla.
 *
 * Gestionada desde /developer (solo acceso con TOTP).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Nombre legible: "Pacientes", "Expedientes"
            $table->string('nombre', 100)->comment('Nombre visible del módulo');

            // Identificador único (= permisos.modulo): "pacientes", "expedientes"
            $table->string('slug', 50)->unique()->comment('Slug único, coincide con permisos.modulo');

            // URL base del módulo: "/pacientes"
            $table->string('url', 255)->nullable()->comment('URL base del módulo');

            // Descripción larga
            $table->text('descripcion')->nullable()->comment('Descripción del módulo');

            // Icono Tabler: "ti ti-users"
            $table->string('icono', 60)->default('ti ti-box')->comment('Clase de icono Tabler');

            // Orden de aparición en menú/listados
            $table->smallInteger('orden')->default(0)->comment('Orden de visualización');

            // Si está activo en el sistema
            $table->boolean('activo')->default(true)->comment('Módulo activo en el sistema');

            $table->timestamps();

            $table->index('activo');
            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
