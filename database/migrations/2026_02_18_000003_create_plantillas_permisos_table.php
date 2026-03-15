<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Migración: Creación de tabla de plantillas de permisos
 * 
 * Propósito: Almacenar plantillas predefinidas para asignación rápida de permisos
 * Las plantillas son conjuntos de permisos que se pueden asignar de una vez
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo crea la tabla de plantillas
 * - DRY: Evita tener que asignar permisos uno por uno repetidamente
 * - Documentación exhaustiva
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecutar la migración.
     * 
     * Crea la tabla 'plantillas_permisos' con los siguientes campos:
     * - id: PK autoincremental
     * - nombre: Nombre de la plantilla (Admin, Recepcionista, Doctor, etc.)
     * - descripcion: Descripción detallada
     * - es_sistema: true = plantilla del sistema (no editable), false = editable
     * - es_activa: true = plantilla disponible, false = plantilla deshabilitada
     * - orden: Orden de visualización en la UI
     * - colores: Color hexadecimal para identificar visualmente la plantilla
     * - timestamps: created_at y updated_at
     */
    public function up(): void
    {
        Schema::create('plantillas_permisos', function (Blueprint $table) {
            // PK autoincremental
            $table->bigIncrements('id')->comment('PK, autoincremental');
            
            // Nombre único de la plantilla
            // Ejemplos: 'Administrador', 'Recepcionista', 'Doctor', 'Asistente'
            $table->string('nombre', 100)->unique()->comment('Nombre único de la plantilla');
            
            // Descripción del propósito de la plantilla
            $table->text('descripcion')->nullable()->comment('Descripción de la plantilla');
            
            // Flag para identificar plantillas del sistema (no eliminables/editables por usuarios)
            // Las plantillas de sistema solo se pueden modificar vía código/migraciones
            $table->boolean('es_sistema')->default(false)->comment('Es plantilla del sistema (no editable por usuarios)');
            
            // Flag para activar/desactivar plantillas
            // Si es false, la plantilla no aparece en la UI de asignación
            $table->boolean('es_activa')->default(true)->comment('Plantilla activa (visible en UI)');
            
            // Orden de visualización en la UI (menor número = aparece primero)
            // Útil para mostrar plantillas más comunes primero
            $table->smallInteger('orden')->default(0)->comment('Orden de visualización en UI');
            
            // Color hexadecimal para identificación visual en la UI
            // Ejemplo: '#667eea' para admin, '#11998e' para doctor
            $table->string('color', 7)->default('#667eea')->comment('Color hexadecimal para UI');
            
            // Icono opcional para identificación visual
            $table->string('icono', 50)->default('ti ti-shield')->comment('Icono CSS para UI');
            
            // Timestamps estándar
            $table->timestamps();
            
            // Índice por es_sistema para filtrar plantillas editables
            $table->index('es_sistema', 'idx_es_sistema');
            
            // Índice por es_activa para filtrar plantillas disponibles
            $table->index('es_activa', 'idx_es_activa');
            
            // Índice por orden para ordenar en consultas
            $table->index('orden', 'idx_orden');
        });
    }

    /**
     * Revertir la migración.
     * 
     * Elimina la tabla 'plantillas_permisos' si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantillas_permisos');
    }
};
