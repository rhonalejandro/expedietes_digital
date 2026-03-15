<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Migración: Creación de tabla de detalle de plantilla de permisos
 * 
 * Propósito: Relacionar plantillas con los permisos que incluyen
 * Cada registro representa un permiso dentro de una plantilla
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo crea la tabla de detalle de plantillas
 * - Documentación exhaustiva
 * - Open/Closed: Permite agregar más metadatos en el futuro sin modificar
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecutar la migración.
     * 
     * Crea la tabla 'plantilla_permiso_detalle' con los siguientes campos:
     * - plantilla_id: FK a plantillas_permisos (PK compuesta)
     * - permiso_id: FK a permisos (PK compuesta)
     * - creado_por: FK a usuarios (quién agregó este permiso a la plantilla)
     * - notas: Notas internas sobre este permiso en la plantilla
     * - timestamps: created_at y updated_at
     * 
     * Incluye índices para optimizar consultas de plantillas y permisos.
     */
    public function up(): void
    {
        Schema::create('plantilla_permiso_detalle', function (Blueprint $table) {
            // FK a plantillas_permisos - plantilla padre
            $table->unsignedBigInteger('plantilla_id')->comment('FK a plantillas_permisos');
            
            // FK a permisos - permiso incluido en la plantilla
            $table->unsignedBigInteger('permiso_id')->comment('FK a permisos');
            
            // FK a usuarios - usuario que configuró la plantilla (auditoría)
            $table->unsignedBigInteger('creado_por')->nullable()->comment('FK a usuarios - Quién configuró este detalle');
            
            // Notas internas sobre por qué este permiso está en esta plantilla
            $table->text('notas')->nullable()->comment('Notas internas sobre este permiso en la plantilla');
            
            // Timestamps estándar
            $table->timestamps();
            
            // PK compuesta: un permiso no puede estar dos veces en la misma plantilla
            $table->primary(['plantilla_id', 'permiso_id'], 'pk_plantilla_permiso');
            
            // FK con restricciones apropiadas
            // ON DELETE CASCADE: si se elimina una plantilla, se eliminan sus detalles
            $table->foreign('plantilla_id')
                  ->references('id')
                  ->on('plantillas_permisos')
                  ->onDelete('cascade')
                  ->comment('Relación con plantillas_permisos');
            
            // ON DELETE CASCADE: si se elimina un permiso, se elimina de las plantillas
            $table->foreign('permiso_id')
                  ->references('id')
                  ->on('permisos')
                  ->onDelete('cascade')
                  ->comment('Relación con permisos');
            
            // FK opcional para auditoría
            // ON DELETE SET NULL: si se elimina el usuario, se mantiene el registro
            $table->foreign('creado_por')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('set null')
                  ->comment('Relación con usuario que creó el detalle (auditoría)');
            
            // Índice por plantilla para obtener todos los permisos de una plantilla
            $table->index('plantilla_id', 'idx_plantilla');
            
            // Índice por permiso para saber en qué plantillas está un permiso
            $table->index('permiso_id', 'idx_permiso_plantilla');
        });
    }

    /**
     * Revertir la migración.
     * 
     * Elimina la tabla 'plantilla_permiso_detalle' si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantilla_permiso_detalle');
    }
};
