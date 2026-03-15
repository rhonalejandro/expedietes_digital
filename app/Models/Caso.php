<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Caso
 * Representa un caso médico asociado a un paciente
 */
class Caso extends Model
{
    // Relación muchos a uno con Paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    // Relación muchos a uno con Especialista
    public function especialista()
    {
        return $this->belongsTo(Especialista::class, 'especialista_id');
    }

    // Relación uno a muchos con Consultas
    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }

    // Relación muchos a uno con Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Relación uno a muchos con Expedientes
    public function expedientes()
    {
        return $this->hasMany(Expediente::class);
    }
}
