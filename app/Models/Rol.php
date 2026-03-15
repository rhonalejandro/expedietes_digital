<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Modelo: Rol (Actualizado)
 * 
 * Propósito: Representar roles de usuario con integración a sistema de permisos
 * Los roles ahora son etiquetas que pueden tener plantillas de permisos asociadas
 * 
 * Cambios realizados:
 * - Agregada relación con PlantillaPermiso
 * - Agregados métodos para verificar si es rol del sistema
 * - Agregados scopes para filtrar roles
 * 
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Rol
 * Representa los roles de usuario en el sistema
 * 
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property int|null $plantilla_id
 * @property bool $es_sistema
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Rol extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'plantilla_id',
        'es_sistema',
    ];

    /**
     * Los atributos que deben ser transformados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'es_sistema' => 'boolean',
        ];
    }

    // ================================
    // RELACIONES
    // ================================

    /**
     * Relación muchos a muchos con Usuarios.
     * 
     * @return BelongsToMany
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuario_rol');
    }

    /**
     * Relación uno a muchos con PlantillaPermiso.
     * 
     * Un rol puede tener una plantilla que define sus permisos base.
     * 
     * @return BelongsTo
     */
    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(PlantillaPermiso::class, 'plantilla_id');
    }

    // ================================
    // MÉTODOS DE UTILIDAD
    // ================================

    /**
     * Verificar si el rol es del sistema (no editable).
     * 
     * @return bool
     */
    public function esSistema(): bool
    {
        return $this->es_sistema;
    }

    /**
     * Verificar si el rol es editable por usuarios.
     * 
     * @return bool
     */
    public function esEditable(): bool
    {
        return !$this->es_sistema;
    }

    /**
     * Obtener los permisos de la plantilla asociada.
     * 
     * @return \Illuminate\Support\Collection<int, Permiso>
     */
    public function getPermisos()
    {
        if (!$this->plantilla) {
            return collect();
        }
        
        return $this->plantilla->permisos()->get();
    }

    /**
     * Obtener el nombre formateado para UI.
     * 
     * @return string
     */
    public function getLabelAttribute(): string
    {
        return $this->nombre . ($this->es_sistema ? ' (Sistema)' : '');
    }

    // ================================
    // SCOPES
    // ================================

    /**
     * Scope para filtrar roles que son del sistema.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSistema($query)
    {
        return $query->where('es_sistema', true);
    }

    /**
     * Scope para filtrar roles que no son del sistema (editables).
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoSistema($query)
    {
        return $query->where('es_sistema', false);
    }

    /**
     * Scope para filtrar roles con plantilla asociada.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConPlantilla($query)
    {
        return $query->whereNotNull('plantilla_id');
    }

    /**
     * Scope para filtrar roles sin plantilla asociada.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSinPlantilla($query)
    {
        return $query->whereNull('plantilla_id');
    }

    /**
     * Scope para ordenar roles por nombre.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('nombre');
    }

    /**
     * Obtener todos los roles editables (no sistema).
     * 
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function getRolesEditables()
    {
        return self::noSistema()->ordenados()->get();
    }

    /**
     * Obtener todos los roles del sistema.
     * 
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function getRolesSistema()
    {
        return self::sistema()->ordenados()->get();
    }
}
