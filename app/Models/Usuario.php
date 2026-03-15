<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Modelo: Usuario (Actualizado)
 * 
 * Propósito: Representar a los usuarios del sistema con sistema de permisos
 * Se agregaron relaciones y métodos para gestión de permisos
 * 
 * Cambios realizados:
 * - Agregada relación muchos a muchos con Permiso
 * - Agregada relación con PlantillaPermiso a través de roles
 * - Agregados métodos para verificar permisos (hasPermission, canView, canEdit, etc.)
 * - Agregados scopes para filtrar usuarios por permisos
 * 
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * Modelo Usuario
 * Representa a los usuarios del sistema (login/acceso)
 * 
 * @property int $id
 * @property int $persona_id
 * @property string $nombre
 * @property string $email
 * @property string $password
 * @property bool $estado
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'persona_id',
        'nombre',
        'email',
        'password',
        'estado',
    ];

    /**
     * Los atributos que deben estar ocultos para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser transformados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    // ================================
    // RELACIONES EXISTENTES
    // ================================

    // Relación uno a uno con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // Relación muchos a muchos con Rol
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol');
    }

    // Relación muchos a muchos con Sucursal
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'usuario_sucursal');
    }

    // ================================
    // NUEVAS RELACIONES PARA PERMISOS
    // ================================

    /**
     * Obtener los permisos directos del usuario.
     * 
     * Relación muchos a muchos a través de la tabla pivote usuario_permiso.
     * 
     * @return BelongsToMany
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permiso::class,
            'usuario_permiso',
            'usuario_id',
            'permiso_id'
        )
        ->withPivot('asignado_por', 'fecha_asignacion', 'observaciones')
        ->withTimestamps();
    }

    /**
     * Obtener todos los permisos del usuario (directos + por rol + por plantilla).
     * 
     * Este método combina:
     * 1. Permisos asignados directamente al usuario
     * 2. Permisos de las plantillas asociadas a sus roles
     * 
     * @return Collection<int, Permiso>
     */
    public function getAllPermisos(): Collection
    {
        // Obtener permisos directos
        $permisosDirectos = $this->permisos()->get();
        
        // Obtener permisos de plantillas de roles
        $permisosPorRol = Collection::make();
        
        foreach ($this->roles as $rol) {
            if ($rol->plantilla) {
                $permisosPorRol = $permisosPorRol->merge(
                    $rol->plantilla->permisos()->get()
                );
            }
        }
        
        // Combinar y eliminar duplicados por ID
        return $permisosDirectos->merge($permisosPorRol)
            ->unique('id')
            ->values();
    }

    /**
     * Obtener los permisos agrupados por módulo.
     * 
     * @return array<string, Collection<int, Permiso>>
     */
    public function getPermisosPorModulo(): array
    {
        $permisos = $this->getAllPermisos();
        
        return $permisos->groupBy('modulo')->toArray();
    }

    // ================================
    // MÉTODOS PARA VERIFICAR PERMISOS
    // ================================

    /**
     * Verificar si el usuario tiene un permiso específico.
     * 
     * @param string $modulo
     * @param string|null $codigo (null para cualquier permiso del módulo)
     * @return bool
     */
    public function hasPermission(string $modulo, ?string $codigo = null): bool
    {
        $permisos = $this->getAllPermisos();
        
        if ($codigo === null) {
            // Verificar si tiene algún permiso del módulo
            return $permisos->where('modulo', $modulo)->isNotEmpty();
        }
        
        // Verificar si tiene el permiso específico
        return $permisos->contains(function ($permiso) use ($modulo, $codigo) {
            return $permiso->modulo === $modulo && $permiso->codigo === $codigo;
        });
    }

    /**
     * Verificar si puede ver (leer) un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    public function canView(string $modulo): bool
    {
        return $this->hasPermission($modulo, 'view');
    }

    /**
     * Verificar si puede crear en un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    public function canCreate(string $modulo): bool
    {
        return $this->hasPermission($modulo, 'create');
    }

    /**
     * Verificar si puede editar en un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    public function canEdit(string $modulo): bool
    {
        return $this->hasPermission($modulo, 'edit');
    }

    /**
     * Verificar si puede eliminar en un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    public function canDelete(string $modulo): bool
    {
        return $this->hasPermission($modulo, 'delete');
    }

    /**
     * Verificar si tiene un permiso granular específico.
     * 
     * @param string $modulo
     * @param string $permisoGranular
     * @return bool
     */
    public function canDo(string $modulo, string $permisoGranular): bool
    {
        return $this->hasPermission($modulo, $permisoGranular);
    }

    /**
     * Verificar si tiene acceso completo a un módulo (todos los permisos CRUD).
     * 
     * @param string $modulo
     * @return bool
     */
    public function hasFullAccess(string $modulo): bool
    {
        return $this->canView($modulo) &&
               $this->canCreate($modulo) &&
               $this->canEdit($modulo) &&
               $this->canDelete($modulo);
    }

    /**
     * Verificar si es administrador (tiene todos los permisos).
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        // Verificar si tiene el rol de administrador o permiso especial
        return $this->roles()->where('nombre', 'Administrador')->exists() ||
               $this->hasPermission('*', '*');
    }

    // ================================
    // SCOPES
    // ================================

    /**
     * Scope para filtrar usuarios con un permiso específico.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $modulo
     * @param string|null $codigo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPermission($query, string $modulo, ?string $codigo = null)
    {
        if ($codigo === null) {
            return $query->whereHas('permisos', function ($q) use ($modulo) {
                $q->where('modulo', $modulo);
            });
        }
        
        return $query->whereHas('permisos', function ($q) use ($modulo, $codigo) {
            $q->where('modulo', $modulo)->where('codigo', $codigo);
        });
    }

    /**
     * Scope para filtrar usuarios activos.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para filtrar usuarios inactivos.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactivos($query)
    {
        return $query->where('estado', false);
    }
}
