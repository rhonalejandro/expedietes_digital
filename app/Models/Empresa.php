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
        'colores_estatus',
    ];

    protected $casts = [
        'estado'          => 'boolean',
        'redes_sociales'  => 'array',
        'colores_estatus' => 'array',
    ];

    /** Colores por defecto si no se han configurado */
    public const COLORES_DEFAULT = [
        'pendiente'   => '#64748b',
        'confirmada'  => '#2f8a59',
        'en_consulta' => '#2563eb',
        'atendida'    => '#2d3748',
        'cancelada'   => '#c53030',
        'no_asistio'  => '#c05621',
    ];

    public function getColoresEstatusAttribute($value): array
    {
        $guardados = $value ? json_decode($value, true) : [];
        return array_merge(self::COLORES_DEFAULT, $guardados ?? []);
    }

    // Relación uno a muchos con Sucursales
    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }
}
