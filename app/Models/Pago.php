<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Pago
 * Representa los pagos realizados por los pacientes
 */
class Pago extends Model
{
    // Relación muchos a uno con Cita
    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    // Relación muchos a uno con Paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}
