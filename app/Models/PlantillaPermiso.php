<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Modelo: PlantillaPermiso
 * 
 * Propósito: Representar una plantilla de permisos predefinida
 * Las plantillas permiten asignar múltiples permisos de una vez
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo representa plantillas de permisos
 * - DRY: Relaciones y scopes reutilizables
 * - Documentación exhaustiva
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property bool $es_sistema
 * @property bool $es_activa
 * @property int $orden
 * @property string $color
 * @property string $icono
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|PlantillaPermiso whereEsSistema(bool $esSistema)
 * @method static \Illuminate\Database\Eloquent\Builder|PlantillaPermiso whereEsActiva(bool $esActiva)
 * @method static \Illuminate\Database\Eloquent\Builder|PlantillaPermiso activas()
 * @method static \Illuminate\Database\Eloquent\Builder|PlantillaPermiso ordenadas()
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class PlantillaPermiso
 * 
 * Modelo que representa una plantilla de permisos predefinida.
 * 
 * @package App\Models
 */
class PlantillaPermiso extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'plantillas_permisos';

    /**
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'es_sistema',
        'es_activa',
        'orden',
        'color',
        'icono',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'es_sistema' => 'boolean',
        'es_activa' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Obtener los permisos incluidos en esta plantilla.
     * 
     * Relación muchos a muchos a través de la tabla pivote plantilla_permiso_detalle.
     * 
     * @return BelongsToMany
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permiso::class,
            'plantilla_permiso_detalle',
            'plantilla_id',
            'permiso_id'
        )
        ->withPivot('creado_por', 'notas')
        ->withTimestamps();
    }

    /**
     * Obtener los roles que usan esta plantilla.
     * 
     * Relación uno a muchos con roles.
     * 
     * @return HasMany
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Rol::class, 'plantilla_id');
    }

    /**
     * Scope para filtrar plantillas que son del sistema.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereEsSistema($query)
    {
        return $query->where('es_sistema', true);
    }

    /**
     * Scope para filtrar plantillas activas.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivas($query)
    {
        return $query->where('es_activa', true);
    }

    /**
     * Scope para filtrar plantillas inactivas.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactivas($query)
    {
        return $query->where('es_activa', false);
    }

    /**
     * Scope para ordenar plantillas por orden de visualización.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    /**
     * Obtener todas las plantillas activas ordenadas.
     * 
     * @param bool $soloSistema
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function getPlantillasActivas(bool $soloSistema = false)
    {
        $query = self::where('es_activa', true);
        
        if ($soloSistema) {
            $query->where('es_sistema', true);
        }
        
        return $query->ordenadas()->get();
    }

    /**
     * Agregar un permiso a esta plantilla.
     * 
     * @param int|Permiso $permiso
     * @param array $pivotData
     * @return bool
     */
    public function agregarPermiso($permiso, array $pivotData = []): bool
    {
        $permisoId = $permiso instanceof Permiso ? $permiso->id : $permiso;
        
        // Verificar si ya existe para evitar duplicados
        if ($this->permisos()->where('permiso_id', $permisoId)->exists()) {
            return false;
        }
        
        $this->permisos()->attach($permisoId, $pivotData);
        
        return true;
    }

    /**
     * Remover un permiso de esta plantilla.
     * 
     * @param int|Permiso $permiso
     * @return bool
     */
    public function removerPermiso($permiso): bool
    {
        $permisoId = $permiso instanceof Permiso ? $permiso->id : $permiso;
        
        if (!$this->permisos()->where('permiso_id', $permisoId)->exists()) {
            return false;
        }
        
        $this->permisos()->detach($permisoId);
        
        return true;
    }

    /**
     * Verificar si esta plantilla es editable por usuarios.
     * 
     * Las plantillas del sistema no son editables.
     * 
     * @return bool
     */
    public function esEditable(): bool
    {
        return !$this->es_sistema;
    }

    /**
     * Obtener atributos CSS para la plantilla.
     * 
     * @return string
     */
    public function getCssStyleAttribute(): string
    {
        return "background-color: {$this->color}; color: #ffffff;";
    }

    /**
     * Obtener clase CSS para badges.
     * 
     * @return string
     */
    public function getBadgeClassAttribute(): string
    {
        return 'badge-custom-' . $this->id;
    }
}
