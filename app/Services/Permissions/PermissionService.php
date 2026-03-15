<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Servicio: PermissionService
 * 
 * Propósito: Lógica de negocio para gestión de permisos
 * Centraliza todas las operaciones relacionadas con permisos
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo maneja lógica de permisos
 * - Dependency Inversion: Usa inyección de dependencias
 * - DRY: Evita duplicación de lógica de permisos
 * - Documentación exhaustiva
 * 
 * @package App\Services\Permissions
 */

namespace App\Services\Permissions;

use App\Models\Permiso;
use App\Models\PlantillaPermiso;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Class PermissionService
 * 
 * Servicio para gestión de permisos del sistema.
 * 
 * @package App\Services\Permissions
 */
class PermissionService
{
    /**
     * Cache de permisos por usuario (en minutos).
     * 
     * @var int
     */
    protected const CACHE_TTL = 120;

    /**
     * Registrar un nuevo permiso en el sistema.
     * 
     * @param string $modulo
     * @param string $codigo
     * @param string $nombre
     * @param string|null $descripcion
     * @param string $tipo
     * @return Permiso
     */
    public function registerPermission(
        string $modulo,
        string $codigo,
        string $nombre,
        ?string $descripcion = null,
        string $tipo = Permiso::TIPO_GENERAL
    ): Permiso {
        // Verificar si ya existe para evitar duplicados
        $existing = Permiso::where('modulo', $modulo)
                          ->where('codigo', $codigo)
                          ->first();
        
        if ($existing) {
            return $existing;
        }
        
        return Permiso::create([
            'modulo' => $modulo,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'estado' => true,
        ]);
    }

    /**
     * Registrar múltiples permisos de una vez.
     * 
     * @param array<int, array<string, string>> $permisos
     * @return Collection<int, Permiso>
     */
    public function registerPermissions(array $permisos): Collection
    {
        $registered = collect();
        
        foreach ($permisos as $permisoData) {
            $registered->push($this->registerPermission(
                $permisoData['modulo'],
                $permisoData['codigo'],
                $permisoData['nombre'],
                $permisoData['descripcion'] ?? null,
                $permisoData['tipo'] ?? Permiso::TIPO_GENERAL
            ));
        }
        
        return $registered;
    }

    /**
     * Asignar un permiso a un usuario directamente.
     * 
     * @param Usuario $usuario
     * @param Permiso|int $permiso
     * @param Usuario|null $asignadoPor
     * @param string|null $observaciones
     * @return bool
     */
    public function assignPermissionToUser(
        Usuario $usuario,
        $permiso,
        ?Usuario $asignadoPor = null,
        ?string $observaciones = null
    ): bool {
        $permisoId = $permiso instanceof Permiso ? $permiso->id : $permiso;
        
        // Verificar si ya tiene el permiso
        if ($usuario->permisos()->where('permiso_id', $permisoId)->exists()) {
            return false;
        }
        
        // Asignar permiso con metadatos
        $usuario->permisos()->attach($permisoId, [
            'asignado_por' => $asignadoPor?->id,
            'observaciones' => $observaciones,
            'fecha_asignacion' => now(),
        ]);
        
        // Limpiar caché de permisos del usuario
        $this->clearUserPermissionsCache($usuario->id);
        
        return true;
    }

    /**
     * Remover un permiso de un usuario.
     * 
     * @param Usuario $usuario
     * @param Permiso|int $permiso
     * @return bool
     */
    public function removePermissionFromUser(Usuario $usuario, $permiso): bool
    {
        $permisoId = $permiso instanceof Permiso ? $permiso->id : $permiso;
        
        if (!$usuario->permisos()->where('permiso_id', $permisoId)->exists()) {
            return false;
        }
        
        $usuario->permisos()->detach($permisoId);
        
        // Limpiar caché
        $this->clearUserPermissionsCache($usuario->id);
        
        return true;
    }

    /**
     * Asignar una plantilla de permisos a un usuario.
     * 
     * @param Usuario $usuario
     * @param PlantillaPermiso|int $plantilla
     * @param Usuario|null $asignadoPor
     * @return bool
     */
    public function assignTemplateToUser(
        Usuario $usuario,
        $plantilla,
        ?Usuario $asignadoPor = null
    ): bool {
        $plantillaId = $plantilla instanceof PlantillaPermiso ? $plantilla->id : $plantilla;
        
        $plantillaModel = PlantillaPermiso::find($plantillaId);
        
        if (!$plantillaModel) {
            return false;
        }
        
        // Obtener todos los permisos de la plantilla
        $permisosIds = $plantillaModel->permisos()->pluck('permisos.id');
        
        // Asignar todos los permisos
        foreach ($permisosIds as $permisoId) {
            $this->assignPermissionToUser(
                $usuario,
                $permisoId,
                $asignadoPor,
                "Asignado desde plantilla: {$plantillaModel->nombre}"
            );
        }
        
        return true;
    }

    /**
     * Obtener todos los permisos de un usuario.
     * 
     * @param int $usuarioId
     * @param bool $forceRefresh
     * @return Collection<int, Permiso>
     */
    public function getUserPermissions(int $usuarioId, bool $forceRefresh = false): Collection
    {
        $cacheKey = "user_permissions_{$usuarioId}";
        
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }
        
        return Cache::remember(
            $cacheKey,
            now()->addMinutes(self::CACHE_TTL),
            function () use ($usuarioId) {
                $usuario = Usuario::with('permisos', 'roles.plantilla.permisos')->find($usuarioId);
                
                if (!$usuario) {
                    return collect();
                }
                
                return $usuario->getAllPermisos();
            }
        );
    }

    /**
     * Verificar si un usuario tiene un permiso específico.
     * 
     * @param int $usuarioId
     * @param string $modulo
     * @param string|null $codigo
     * @return bool
     */
    public function userHasPermission(int $usuarioId, string $modulo, ?string $codigo = null): bool
    {
        $permisos = $this->getUserPermissions($usuarioId);
        
        if ($codigo === null) {
            return $permisos->where('modulo', $modulo)->isNotEmpty();
        }
        
        return $permisos->contains(function ($permiso) use ($modulo, $codigo) {
            return $permiso->modulo === $modulo && $permiso->codigo === $codigo;
        });
    }

    /**
     * Obtener todos los permisos agrupados por módulo.
     * 
     * @param bool $soloActivos
     * @return array<string, Collection<int, Permiso>>
     */
    public function getAllPermissionsGroupedByModule(bool $soloActivos = true): array
    {
        $query = Permiso::with('usuarios', 'plantillas');
        
        if ($soloActivos) {
            $query->where('estado', true);
        }
        
        return $query->get()->groupBy('modulo')->toArray();
    }

    /**
     * Obtener permisos de un módulo específico.
     * 
     * @param string $modulo
     * @param bool $soloActivos
     * @return Collection<int, Permiso>
     */
    public function getPermissionsByModule(string $modulo, bool $soloActivos = true): Collection
    {
        $query = Permiso::where('modulo', $modulo);
        
        if ($soloActivos) {
            $query->where('estado', true);
        }
        
        return $query->orderBy('tipo')->orderBy('nombre')->get();
    }

    /**
     * Activar un permiso.
     * 
     * @param int $permisoId
     * @return bool
     */
    public function activatePermission(int $permisoId): bool
    {
        $permiso = Permiso::find($permisoId);
        
        if (!$permiso) {
            return false;
        }
        
        $permiso->update(['estado' => true]);
        
        // Limpiar caché de todos los usuarios (este permiso puede afectar a muchos)
        Cache::tags(['permissions'])->flush();
        
        return true;
    }

    /**
     * Desactivar un permiso.
     * 
     * @param int $permisoId
     * @return bool
     */
    public function deactivatePermission(int $permisoId): bool
    {
        $permiso = Permiso::find($permisoId);
        
        if (!$permiso) {
            return false;
        }
        
        $permiso->update(['estado' => false]);
        
        // Limpiar caché
        Cache::tags(['permissions'])->flush();
        
        return true;
    }

    /**
     * Eliminar un permiso (solo si no está en uso).
     * 
     * @param int $permisoId
     * @return bool
     * @throws \Exception
     */
    public function deletePermission(int $permisoId): bool
    {
        $permiso = Permiso::find($permisoId);
        
        if (!$permiso) {
            return false;
        }
        
        // Verificar si está en uso
        $usuarioCount = $permiso->usuarios()->count();
        $plantillaCount = $permiso->plantillas()->count();
        
        if ($usuarioCount > 0 || $plantillaCount > 0) {
            throw new \Exception(
                "No se puede eliminar el permiso '{$permiso->nombre}' porque está en uso " .
                "por {$usuarioCount} usuario(s) y {$plantillaCount} plantilla(s)."
            );
        }
        
        $permiso->delete();
        
        // Limpiar caché
        Cache::tags(['permissions'])->flush();
        
        return true;
    }

    /**
     * Limpiar el caché de permisos de un usuario.
     * 
     * @param int $usuarioId
     * @return void
     */
    public function clearUserPermissionsCache(int $usuarioId): void
    {
        Cache::forget("user_permissions_{$usuarioId}");
    }

    /**
     * Limpiar el caché de todos los permisos.
     * 
     * @return void
     */
    public function clearAllPermissionsCache(): void
    {
        Cache::tags(['permissions'])->flush();
    }

    /**
     * Sincronizar permisos de un usuario (reemplaza todos los existentes).
     * 
     * @param Usuario $usuario
     * @param array<int, int> $permisosIds
     * @param Usuario|null $asignadoPor
     * @return array<string, int>
     */
    public function syncUserPermissions(
        Usuario $usuario,
        array $permisosIds,
        ?Usuario $asignadoPor = null
    ): array {
        $result = [
            'attached' => 0,
            'detached' => 0,
            'updated' => 0,
        ];
        
        DB::transaction(function () use ($usuario, $permisosIds, $asignadoPor, &$result) {
            // Obtener permisos actuales
            $currentIds = $usuario->permisos()->pluck('permiso_id')->toArray();
            
            // Calcular diferencias
            $toAttach = array_diff($permisosIds, $currentIds);
            $toDetach = array_diff($currentIds, $permisosIds);
            
            // Adjuntar nuevos
            if (!empty($toAttach)) {
                $attachData = [];
                foreach ($toAttach as $permisoId) {
                    $attachData[$permisoId] = [
                        'asignado_por' => $asignadoPor?->id,
                        'fecha_asignacion' => now(),
                    ];
                }
                $usuario->permisos()->attach($attachData);
                $result['attached'] = count($toAttach);
            }
            
            // Remover los que ya no corresponden
            if (!empty($toDetach)) {
                $usuario->permisos()->detach($toDetach);
                $result['detached'] = count($toDetach);
            }
        });
        
        // Limpiar caché
        $this->clearUserPermissionsCache($usuario->id);
        
        return $result;
    }

    /**
     * Obtener usuarios con un permiso específico.
     * 
     * @param string $modulo
     * @param string $codigo
     * @return Collection<int, Usuario>
     */
    public function getUsersWithPermission(string $modulo, string $codigo): Collection
    {
        $permiso = Permiso::where('modulo', $modulo)
                         ->where('codigo', $codigo)
                         ->first();
        
        if (!$permiso) {
            return collect();
        }
        
        return $permiso->usuarios()->with('persona', 'roles')->get();
    }

    /**
     * Contar usuarios con un permiso específico.
     * 
     * @param string $modulo
     * @param string $codigo
     * @return int
     */
    public function countUsersWithPermission(string $modulo, string $codigo): int
    {
        $permiso = Permiso::where('modulo', $modulo)
                         ->where('codigo', $codigo)
                         ->first();
        
        if (!$permiso) {
            return 0;
        }
        
        return $permiso->usuarios()->count();
    }

    /**
     * Obtener estadísticas de permisos.
     * 
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        return [
            'total_permisos' => Permiso::count(),
            'permisos_activos' => Permiso::where('estado', true)->count(),
            'permisos_inactivos' => Permiso::where('estado', false)->count(),
            'permisos_generales' => Permiso::where('tipo', Permiso::TIPO_GENERAL)->count(),
            'permisos_granulares' => Permiso::where('tipo', Permiso::TIPO_GRANULAR)->count(),
            'total_modulos' => Permiso::distinct('modulo')->count(),
            'modulos' => Permiso::distinct()->pluck('modulo')->sort()->values()->toArray(),
        ];
    }
}
