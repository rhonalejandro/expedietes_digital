<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo HorarioDoctor
 * Representa los horarios de los doctores por sucursal
 */
class HorarioDoctor extends Model
{
    // Relación muchos a uno con Doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Relación muchos a uno con Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
