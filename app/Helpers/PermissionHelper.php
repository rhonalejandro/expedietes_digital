<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Helper: PermissionHelper
 * 
 * Propósito: Funciones globales para verificación de permisos
 * Proporciona funciones helper de uso común en todo el sistema
 * 
 * Uso en controladores:
 *   if (hasPermission('clients')) { ... }
 *   if (canEdit('products')) { ... }
 *   if (canDo('products', 'taxes')) { ... }
 * 
 * Uso en vistas Blade:
 *   @if(hasPermission('clients')) ... @endif
 *   @canView('clients') ... @endcanView
 * 
 * Principios aplicados:
 * - DRY: Centraliza lógica de verificación de permisos
 * - Single Responsibility: Solo maneja helpers de permisos
 * - Documentación exhaustiva
 * 
 * @package App\Helpers
 */

namespace App\Helpers;

use App\Models\Permiso;
use App\Models\Usuario;
use Illuminate\Support\Collection;

/**
 * Class PermissionHelper
 * 
 * Helper con funciones globales para gestión de permisos.
 * 
 * @package App\Helpers
 */
class PermissionHelper
{
    /**
     * Usuario actual cacheado para la request.
     * 
     * @var Usuario|null
     */
    protected static ?Usuario $currentUser = null;

    /**
     * Obtener el usuario autenticado actual.
     * 
     * @return Usuario|null
     */
    protected static function getCurrentUser(): ?Usuario
    {
        if (self::$currentUser === null) {
            self::$currentUser = auth()->check() ? auth()->user() : null;
        }
        
        return self::$currentUser;
    }

    /**
     * Limpiar el cache del usuario actual.
     * 
     * @return void
     */
    public static function clearCurrentUserCache(): void
    {
        self::$currentUser = null;
    }

    /**
     * Verificar si el usuario autenticado tiene un permiso específico.
     * 
     * @param string $modulo Módulo a verificar (ej: 'clients', 'products')
     * @param string|null $codigo Código del permiso (ej: 'view', 'create') - null para cualquier permiso del módulo
     * @return bool
     * 
     * @example
     *   hasPermission('clients')           // Tiene algún permiso de clientes
     *   hasPermission('clients', 'view')   // Puede ver clientes
     *   hasPermission('products', 'taxes') // Puede gestionar impuestos
     */
    public static function hasPermission(string $modulo, ?string $codigo = null): bool
    {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasPermission($modulo, $codigo);
    }

    /**
     * Verificar si el usuario puede ver (leer) un módulo.
     * 
     * @param string $modulo
     * @return bool
     * 
     * @example
     *   canView('clients')      // Puede ver clientes
     *   canView('products')     // Puede ver productos
     */
    public static function canView(string $modulo): bool
    {
        return self::hasPermission($modulo, 'view');
    }

    /**
     * Verificar si el usuario puede crear en un módulo.
     * 
     * @param string $modulo
     * @return bool
     * 
     * @example
     *   canCreate('clients')    // Puede crear clientes
     *   canCreate('products')   // Puede crear productos
     */
    public static function canCreate(string $modulo): bool
    {
        return self::hasPermission($modulo, 'create');
    }

    /**
     * Verificar si el usuario puede editar en un módulo.
     * 
     * @param string $modulo
     * @return bool
     * 
     * @example
     *   canEdit('clients')      // Puede editar clientes
     *   canEdit('products')     // Puede editar productos
     */
    public static function canEdit(string $modulo): bool
    {
        return self::hasPermission($modulo, 'edit');
    }

    /**
     * Verificar si el usuario puede eliminar en un módulo.
     * 
     * @param string $modulo
     * @return bool
     * 
     * @example
     *   canDelete('clients')    // Puede eliminar clientes
     *   canDelete('products')   // Puede eliminar productos
     */
    public static function canDelete(string $modulo): bool
    {
        return self::hasPermission($modulo, 'delete');
    }

    /**
     * Verificar si el usuario tiene un permiso granular específico.
     * 
     * @param string $modulo
     * @param string $permisoGranular Código del permiso granular
     * @return bool
     * 
     * @example
     *   canDo('products', 'taxes')        // Puede gestionar impuestos
     *   canDo('products', 'pricing')      // Puede cambiar precios
     *   canDo('medical', 'sign_digital')  // Puede firmar digitalmente
     */
    public static function canDo(string $modulo, string $permisoGranular): bool
    {
        return self::hasPermission($modulo, $permisoGranular);
    }

    /**
     * Verificar si el usuario tiene acceso completo a un módulo (todos los permisos CRUD).
     * 
     * @param string $modulo
     * @return bool
     * 
     * @example
     *   hasFullAccess('clients')  // Tiene todos los permisos de clientes
     */
    public static function hasFullAccess(string $modulo): bool
    {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasFullAccess($modulo);
    }

    /**
     * Verificar si el usuario es administrador.
     * 
     * @return bool
     */
    public static function isAdmin(): bool
    {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->isAdmin();
    }

    /**
     * Obtener todos los permisos del usuario autenticado.
     * 
     * @return Collection<int, Permiso>
     */
    public static function getUserPermissions(): Collection
    {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return collect();
        }
        
        return $user->getAllPermisos();
    }

    /**
     * Obtener permisos del usuario agrupados por módulo.
     * 
     * @return array<string, Collection<int, Permiso>>
     */
    public static function getPermisosPorModulo(): array
    {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return [];
        }
        
        return $user->getPermisosPorModulo();
    }

    /**
     * Verificar si el usuario tiene ALGUNO de los permisos listados.
     * 
     * @param array<int, array{0: string, 1?: string}> $permisos Lista de permisos [modulo, codigo?]
     * @return bool
     * 
     * @example
     *   hasAnyPermission([
     *       ['clients', 'view'],
     *       ['clients', 'create']
     *   ])  // Tiene al menos uno de esos permisos
     */
    public static function hasAnyPermission(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            $modulo = $permiso[0];
            $codigo = $permiso[1] ?? null;
            
            if (self::hasPermission($modulo, $codigo)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verificar si el usuario tiene TODOS los permisos listados.
     * 
     * @param array<int, array{0: string, 1?: string}> $permisos Lista de permisos [modulo, codigo?]
     * @return bool
     * 
     * @example
     *   hasAllPermissions([
     *       ['clients', 'view'],
     *       ['clients', 'create'],
     *       ['clients', 'edit']
     *   ])  // Tiene todos esos permisos
     */
    public static function hasAllPermissions(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            $modulo = $permiso[0];
            $codigo = $permiso[1] ?? null;
            
            if (!self::hasPermission($modulo, $codigo)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Abortar si el usuario no tiene el permiso especificado.
     * 
     * @param string $modulo
     * @param string|null $codigo
     * @param string|null $message
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * 
     * @example
     *   abortUnlessHasPermission('clients', 'edit', 'No puedes editar clientes');
     */
    public static function abortUnlessHasPermission(
        string $modulo,
        ?string $codigo = null,
        ?string $message = null
    ): void {
        if (!self::hasPermission($modulo, $codigo)) {
            abort(403, $message ?? 'No tienes permiso para realizar esta acción.');
        }
    }

    /**
     * Abortar si el usuario no puede ver el módulo.
     * 
     * @param string $modulo
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public static function abortUnlessCanView(string $modulo): void
    {
        self::abortUnlessHasPermission($modulo, 'view');
    }

    /**
     * Abortar si el usuario no puede editar el módulo.
     * 
     * @param string $modulo
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public static function abortUnlessCanEdit(string $modulo): void
    {
        self::abortUnlessHasPermission($modulo, 'edit');
    }

    /**
     * Obtener el nombre del usuario actual formateado.
     * 
     * @return string
     */
    public static function getCurrentUserName(): string
    {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return 'Invitado';
        }
        
        return $user->persona->nombre ?? $user->nombre ?? 'Usuario';
    }

    /**
     * Verificar si el usuario actual es el mismo que el especificado.
     * 
     * @param int $usuarioId
     * @return bool
     */
    public static function isCurrentUser(int $usuarioId): bool
    {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->id === $usuarioId;
    }
}
