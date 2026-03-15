<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Doctor
 * Representa a los doctores de la clínica
 */
class Doctor extends Model
{
    // Relación uno a uno con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // Relación uno a muchos con Consultas
    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }

    // Relación muchos a muchos con Sucursal
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'doctor_sucursal');
    }

    // Relación uno a muchos con Horarios
    public function horarios()
    {
        return $this->hasMany(HorarioDoctor::class);
    }

    // Relación uno a muchos con Citas
    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
