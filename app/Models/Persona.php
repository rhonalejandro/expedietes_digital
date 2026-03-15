<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Persona
 *
 * Representa a cualquier persona del sistema (usuario, paciente, doctor, etc.).
 * Usa SoftDeletes: el destroy del módulo de pacientes elimina lógicamente.
 */
class Persona extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_identificacion',
        'identificacion',
        'fecha_nacimiento',
        'contacto',
        'direccion',
        'email',
        'genero',
        'estado',
        'ocupacion',
        'nacionalidad',
        'seguro_medico',
        'contacto_emergencia',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function usuario()
    {
        return $this->hasOne(Usuario::class);
    }

    public function paciente()
    {
        return $this->hasOne(Paciente::class);
    }

    public function especialista()
    {
        return $this->hasOne(Especialista::class);
    }
}
