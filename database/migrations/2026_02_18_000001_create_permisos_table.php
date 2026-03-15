<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Migración: Creación de tabla de permisos
 * 
 * Propósito: Almacenar todos los permisos disponibles en el sistema
 * Cada permiso tiene un módulo y un código único que lo identifica
 * 
 * Principios aplicados:
 * - Single Responsibility: Esta migración solo crea la tabla permisos
 * - Documentación exhaustiva: Cada campo está comentado
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecutar la migración.
     * 
     * Crea la tabla 'permisos' con los siguientes campos:
     * - id: PK autoincremental
     * - modulo: Módulo al que pertenece el permiso (clients, products, etc.)
     * - codigo: Código único del permiso dentro del módulo (view, create, edit, delete, taxes, etc.)
     * - nombre: Nombre legible del permiso
     * - descripcion: Descripción detallada del permiso
     * - tipo: 'general' para permisos CRUD básicos, 'granular' para permisos especiales
     * - estado: true = activo, false = inactivo
     * - timestamps: created_at y updated_at
     */
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            // PK autoincremental
            $table->bigIncrements('id')->comment('PK, autoincremental');
            
            // Módulo al que pertenece el permiso
            // Ejemplos: 'clients', 'products', 'appointments', 'medical_records', etc.
            $table->string('modulo', 50)->comment('Módulo al que pertenece (clients, products, appointments, etc.)');
            
            // Código único del permiso dentro del módulo
            // Ejemplos: 'view', 'create', 'edit', 'delete', 'taxes', 'pricing', 'sign_digital', etc.
            $table->string('codigo', 50)->comment('Código único del permiso (view, create, edit, delete, taxes, pricing, etc.)');
            
            // Nombre legible para mostrar en UI
            // Ejemplo: 'Ver clientes', 'Gestionar impuestos', 'Firmar digitalmente'
            $table->string('nombre', 100)->comment('Nombre legible del permiso');
            
            // Descripción detallada del propósito del permiso
            $table->text('descripcion')->nullable()->comment('Descripción detallada del permiso');
            
            // Tipo de permiso: 'general' (CRUD básico) o 'granular' (permisos especiales)
            $table->string('tipo', 20)->default('general')->comment('Tipo: general (CRUD) o granular (especial)');
            
            // Estado del permiso: true = activo, false = inactivo
            // Permite desactivar permisos sin eliminarlos de la BD
            $table->boolean('estado')->default(true)->comment('Estado: true=activo, false=inactivo');
            
            // Timestamps estándar de Laravel
            $table->timestamps();
            
            // Índice único para evitar permisos duplicados en el mismo módulo
            // Un permiso se identifica por la combinación modulo + codigo
            $table->unique(['modulo', 'codigo'], 'uniq_modulo_codigo');
            
            // Índice por módulo para consultas rápidas de todos los permisos de un módulo
            $table->index('modulo', 'idx_modulo');
            
            // Índice por tipo para filtrar entre generales y granulares
            $table->index('tipo', 'idx_tipo');
            
            // Índice por estado para filtrar permisos activos
            $table->index('estado', 'idx_estado');
        });
    }

    /**
     * Revertir la migración.
     * 
     * Elimina la tabla 'permisos' si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
