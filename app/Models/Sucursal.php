<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Sucursal
 * Representa una sucursal de la empresa
 */
class Sucursal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sucursales';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'direccion',
        'telefono',
        'contacto',  // Para compatibilidad
        'email',
        'encargado',
        'hora_apertura',
        'hora_cierre',
        'estado',
        'imagen',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // Relación muchos a uno con Empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Relación muchos a muchos con Especialistas
    public function especialistas()
    {
        return $this->belongsToMany(Especialista::class, 'especialista_sucursal');
    }

    // Relación muchos a muchos con Usuarios
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_sucursal');
    }

    // Relación uno a muchos con Citas
    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
