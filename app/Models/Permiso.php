<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Modelo: Permiso
 * 
 * Propósito: Representar un permiso del sistema
 * Cada permiso pertenece a un módulo y tiene un código único
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo representa permisos
 * - DRY: Relaciones y scopes reutilizables
 * - Documentación exhaustiva
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $modulo
 * @property string $codigo
 * @property string $nombre
 * @property string|null $descripcion
 * @property string $tipo
 * @property bool $estado
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Permiso whereModulo(string $modulo)
 * @method static \Illuminate\Database\Eloquent\Builder|Permiso whereCodigo(string $codigo)
 * @method static \Illuminate\Database\Eloquent\Builder|Permiso whereTipo(string $tipo)
 * @method static \Illuminate\Database\Eloquent\Builder|Permiso whereEstado(bool $estado)
 * @method static \Illuminate\Database\Eloquent\Builder|Permiso generales()
 * @method static \Illuminate\Database\Eloquent\Builder|Permiso granulares()
 * @method static \Illuminate\Database\Eloquent\Builder|Permiso activos()
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Permiso
 * 
 * Modelo que representa un permiso del sistema.
 * 
 * @package App\Models
 */
class Permiso extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'permisos';

    /**
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'modulo',
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'estado',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Constantes para tipos de permisos.
     * 
     * Estos valores deben coincidir con los almacenados en la BD.
     */
    public const TIPO_GENERAL = 'general';
    public const TIPO_GRANULAR = 'granular';

    /**
     * Constantes para códigos de permisos generales (CRUD básico).
     */
    public const CODIGO_VIEW = 'view';
    public const CODIGO_CREATE = 'create';
    public const CODIGO_EDIT = 'edit';
    public const CODIGO_DELETE = 'delete';

    /**
     * Obtener los usuarios que tienen este permiso.
     * 
     * Relación muchos a muchos a través de la tabla pivote usuario_permiso.
     * 
     * @return BelongsToMany
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuario_permiso',
            'permiso_id',
            'usuario_id'
        )
        ->withPivot('asignado_por', 'fecha_asignacion', 'observaciones')
        ->withTimestamps();
    }

    /**
     * Obtener las plantillas que incluyen este permiso.
     * 
     * Relación muchos a muchos a través de la tabla pivote plantilla_permiso_detalle.
     * 
     * @return BelongsToMany
     */
    public function plantillas(): BelongsToMany
    {
        return $this->belongsToMany(
            PlantillaPermiso::class,
            'plantilla_permiso_detalle',
            'permiso_id',
            'plantilla_id'
        )
        ->withPivot('creado_por', 'notas')
        ->withTimestamps();
    }

    /**
     * Scope para filtrar permisos por módulo.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $modulo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereModulo($query, string $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para filtrar permisos por código.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $codigo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCodigo($query, string $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    /**
     * Scope para filtrar permisos generales (CRUD básico).
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGenerales($query)
    {
        return $query->where('tipo', self::TIPO_GENERAL);
    }

    /**
     * Scope para filtrar permisos granulares (especiales).
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGranulares($query)
    {
        return $query->where('tipo', self::TIPO_GRANULAR);
    }

    /**
     * Scope para filtrar permisos activos.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para filtrar permisos inactivos.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactivos($query)
    {
        return $query->where('estado', false);
    }

    /**
     * Obtener todos los permisos de un módulo específico.
     * 
     * @param string $modulo
     * @param bool $soloActivos
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function getPermisosByModulo(string $modulo, bool $soloActivos = true)
    {
        $query = self::where('modulo', $modulo);
        
        if ($soloActivos) {
            $query->where('estado', true);
        }
        
        return $query->orderBy('tipo')->orderBy('nombre')->get();
    }

    /**
     * Verificar si el permiso es de tipo general (CRUD).
     * 
     * @return bool
     */
    public function esGeneral(): bool
    {
        return $this->tipo === self::TIPO_GENERAL;
    }

    /**
     * Verificar si el permiso es de tipo granular (especial).
     * 
     * @return bool
     */
    public function esGranular(): bool
    {
        return $this->tipo === self::TIPO_GRANULAR;
    }

    /**
     * Obtener el nombre completo del permiso (módulo.codigo).
     * 
     * @return string
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->modulo}.{$this->codigo}";
    }

    /**
     * Obtener etiqueta formateada para UI.
     * 
     * @return string
     */
    public function getLabelAttribute(): string
    {
        return ucfirst($this->codigo) . ' ' . ucfirst($this->modulo);
    }
}
