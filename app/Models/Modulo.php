<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Modelo Modulo
 *
 * Representa un módulo del sistema registrado desde el Developer Panel.
 * El campo `slug` enlaza lógicamente con `permisos.modulo`.
 *
 * @property int    $id
 * @property string $nombre
 * @property string $slug
 * @property string $url
 * @property string $descripcion
 * @property string $icono
 * @property int    $orden
 * @property bool   $activo
 */
class Modulo extends Model
{
    protected $table = 'modulos';

    protected $fillable = [
        'nombre',
        'slug',
        'url',
        'descripcion',
        'icono',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden'  => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // ── Relación lógica con Permiso ───────────────────────────────────────────

    /**
     * Retorna los permisos (acciones) asociados a este módulo.
     * Relación lógica vía slug — no FK física.
     */
    public function permisos(): Collection
    {
        return Permiso::where('modulo', $this->slug)->orderBy('tipo')->orderBy('nombre')->get();
    }

    /**
     * Cuenta los permisos asociados al módulo.
     */
    public function totalAcciones(): int
    {
        return Permiso::where('modulo', $this->slug)->count();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->first();
    }

    /** Retorna todos los módulos activos ordenados para selects. */
    public static function paraSelect(): Collection
    {
        return self::activos()->ordenados()->get();
    }
}
