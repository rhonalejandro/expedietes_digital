<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Migración: Modificación de tabla roles para integrar con sistema de permisos
 * 
 * Propósito: Agregar campos a la tabla roles para que funcione como etiqueta
 * agrupadora de plantillas de permisos, no como limitante
 * 
 * Cambios:
 * - Agrega plantilla_id: FK a plantillas_permisos (permisos por defecto del rol)
 * - Agrega es_sistema: true = rol del sistema (no editable)
 * - Agrega descripcion mejorada
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo modifica la tabla roles
 * - Open/Closed: Extiende la funcionalidad de roles sin romper lo existente
 * - Documentación exhaustiva
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecutar la migración.
     * 
     * Modifica la tabla 'roles' agregando:
     * - plantilla_id: FK a plantillas_permisos (nullable, puede no tener plantilla)
     * - es_sistema: boolean para roles del sistema
     * - Se modifica descripcion para ser más descriptiva
     * 
     * Se mantiene compatibilidad hacia atrás:
     * - Los roles existentes siguen funcionando
     * - plantilla_id es nullable
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // FK opcional a plantillas_permisos
            // Un rol puede tener una plantilla asociada que define sus permisos base
            // Es nullable porque un rol puede existir sin plantilla (permisos manuales)
            $table->unsignedBigInteger('plantilla_id')->nullable()
                  ->after('descripcion')
                  ->comment('FK a plantillas_permisos - Plantilla de permisos asociada');
            
            // Flag para identificar roles del sistema (no eliminables/editables)
            // Los roles de sistema solo se pueden modificar vía código/migraciones
            $table->boolean('es_sistema')->default(false)
                  ->after('plantilla_id')
                  ->comment('Es rol del sistema (no editable por usuarios)');
            
            // Se modifica la columna descripcion existente para aceptar más texto
            // Si ya es TEXT, esto no tendrá efecto, pero asegura consistencia
            $table->text('descripcion')->nullable()->change()
                  ->comment('Descripción detallada del rol y sus responsabilidades');
            
            // FK a plantillas_permisos
            // ON DELETE SET NULL: si se elimina la plantilla, el rol mantiene su nombre
            // pero ya no tendrá permisos automáticos asociados
            $table->foreign('plantilla_id')
                  ->references('id')
                  ->on('plantillas_permisos')
                  ->nullOnDelete()
                  ->comment('Relación con plantilla de permisos');
            
            // Índice para consultas rápidas de roles con plantilla
            // Usamos un nombre único para evitar conflictos
            $table->index('plantilla_id', 'idx_roles_plantilla_id');
            
            // Índice para filtrar roles del sistema
            // Usamos un nombre único para evitar conflictos
            $table->index('es_sistema', 'idx_roles_es_sistema');
        });
    }

    /**
     * Revertir la migración.
     * 
     * Elimina los campos agregados y la FK.
     * Nota: Esto no elimina los datos, solo la estructura agregada.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Eliminar FK
            $table->dropForeign(['plantilla_id']);
            
            // Eliminar índices
            $table->dropIndex('idx_plantilla');
            $table->dropIndex('idx_es_sistema');
            
            // Eliminar columnas agregadas
            $table->dropColumn(['plantilla_id', 'es_sistema']);
            
            // Restaurar descripcion a su estado original (VARCHAR 255)
            $table->string('descripcion', 255)->nullable()->change();
        });
    }
};
