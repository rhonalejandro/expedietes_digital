<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Imagen
 * Representa imágenes asociadas a un historial médico
 */
class Imagen extends Model
{
    // Relación muchos a uno con HistorialMedico
    public function historial()
    {
        return $this->belongsTo(HistorialMedico::class, 'historial_id');
    }
}
