<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Expediente
 * Representa un expediente médico asociado a un caso y cita
 */
class Expediente extends Model
{
    // Relación muchos a uno con Caso
    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }

    // Relación muchos a uno con Cita
    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    // Relación muchos a uno con Especialista
    public function especialista()
    {
        return $this->belongsTo(Especialista::class, 'especialista_id');
    }

    // Relación muchos a uno con Paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    // Relación uno a muchos con HistorialMedico
    public function historiales()
    {
        return $this->hasMany(HistorialMedico::class);
    }
}
