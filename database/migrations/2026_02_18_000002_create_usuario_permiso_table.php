<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Migración: Creación de tabla usuario_permiso (tabla pivote)
 * 
 * Propósito: Relacionar usuarios con sus permisos asignados directamente
 * Esta tabla permite asignación directa sin pasar por roles
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo crea la tabla de relación usuario-permiso
 * - Documentación exhaustiva: Cada campo y índice está comentado
 * - Open/Closed: La estructura permite extensión futura sin modificar
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecutar la migración.
     * 
     * Crea la tabla 'usuario_permiso' con los siguientes campos:
     * - usuario_id: FK a usuarios (PK compuesta)
     * - permiso_id: FK a permisos (PK compuesta)
     * - asignado_por: FK a usuarios (quién asignó el permiso)
     * - fecha_asignacion: Fecha cuando se asignó el permiso
     * - observaciones: Notas opcionales sobre la asignación
     * - timestamps: created_at y updated_at
     * 
     * Incluye índices para optimizar consultas frecuentes.
     */
    public function up(): void
    {
        Schema::create('usuario_permiso', function (Blueprint $table) {
            // FK a usuarios - usuario que recibe el permiso
            $table->unsignedBigInteger('usuario_id')->comment('FK a usuarios - Usuario que recibe el permiso');
            
            // FK a permisos - permiso que se asigna
            $table->unsignedBigInteger('permiso_id')->comment('FK a permisos - Permiso asignado');
            
            // FK a usuarios - usuario que realizó la asignación (auditoría)
            // Puede ser null si la asignación fue automática (ej: por plantilla)
            $table->unsignedBigInteger('asignado_por')->nullable()->comment('FK a usuarios - Quién asignó el permiso (auditoría)');
            
            // Fecha exacta de la asignación
            $table->timestamp('fecha_asignacion')->useCurrent()->comment('Fecha de asignación del permiso');
            
            // Observaciones opcionales sobre por qué se asignó este permiso
            $table->text('observaciones')->nullable()->comment('Observaciones sobre la asignación');
            
            // Timestamps estándar
            $table->timestamps();
            
            // PK compuesta: un usuario no puede tener el mismo permiso dos veces
            $table->primary(['usuario_id', 'permiso_id'], 'pk_usuario_permiso');
            
            // FK con restricciones apropiadas
            // ON DELETE CASCADE: si se elimina un usuario, se eliminan sus permisos
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('cascade')
                  ->comment('Relación con usuarios');
            
            // ON DELETE CASCADE: si se elimina un permiso, se elimina de todos los usuarios
            $table->foreign('permiso_id')
                  ->references('id')
                  ->on('permisos')
                  ->onDelete('cascade')
                  ->comment('Relación con permisos');
            
            // FK opcional para auditoría (quién asignó)
            // ON DELETE SET NULL: si se elimina el usuario que asignó, se mantiene el registro
            $table->foreign('asignado_por')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('set null')
                  ->comment('Relación con usuario que asignó (auditoría)');
            
            // Índice por usuario para consultas rápidas de permisos de un usuario específico
            $table->index('usuario_id', 'idx_usuario');
            
            // Índice por permiso para saber qué usuarios tienen un permiso específico
            $table->index('permiso_id', 'idx_permiso');
            
            // Índice por asignado_por para auditoría (quién ha asignado más permisos)
            $table->index('asignado_por', 'idx_asignado_por');
        });
    }

    /**
     * Revertir la migración.
     * 
     * Elimina la tabla 'usuario_permiso' si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_permiso');
    }
};
