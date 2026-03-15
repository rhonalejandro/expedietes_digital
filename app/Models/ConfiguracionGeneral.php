<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo ConfiguracionGeneral
 * Representa parámetros de configuración global del sistema
 */
class ConfiguracionGeneral extends Model
{
    protected $table = 'configuracion_general';
    protected $fillable = [
        'clave', 'valor', 'descripcion', 'fecha'
    ];
    // Aquí se pueden definir métodos para obtener configuraciones específicas
}
