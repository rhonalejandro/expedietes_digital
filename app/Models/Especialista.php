<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Especialista extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'especialistas';

    protected $fillable = [
        'persona_id',
        'tratamiento',
        'profesion',
        'especialidad',
        'num_colegiado',
        'telefono',
        'email',
        'firma',
        'estado',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'estado'   => 'boolean',
        'password' => 'hashed',
    ];

    // Nombre completo con tratamiento: "Dr. Juan Pérez"
    public function getNombreCompletoAttribute(): string
    {
        $trato = $this->tratamiento ? $this->tratamiento . ' ' : '';
        return $trato . ($this->persona->nombre ?? '') . ' ' . ($this->persona->apellido ?? '');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'especialista_sucursal');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioEspecialista::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'especialista_id');
    }

    public function casos()
    {
        return $this->hasMany(Caso::class, 'especialista_id');
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'especialista_id');
    }

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'especialista_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
