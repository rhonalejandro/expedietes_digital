<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo Empresa
 * Representa una empresa (multiempresa)
 */
class Empresa extends Model
{
    use HasFactory;
    protected $table = 'empresas';
    protected $fillable = [
        'nombre',
        'tipo_identificacion',
        'identificacion',
        'telefono',
        'email',
        'logo',
        'logo_rectangular',
        'pagina_web',
        'redes_sociales',
        'direccion',
        'estado',
        'modo_agenda',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'redes_sociales' => 'array',
    ];

    // Relación uno a muchos con Sucursales
    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }
}
