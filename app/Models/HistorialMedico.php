<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo HistorialMedico
 * Representa la evolución y registros médicos de un expediente
 */
class HistorialMedico extends Model
{
    // Relación muchos a uno con Expediente
    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    // Relación uno a muchos con Imagenes
    public function imagenes()
    {
        return $this->hasMany(Imagen::class, 'historial_id');
    }
}
