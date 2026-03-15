<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Funciones Globales de Permisos
 * 
 * Propósito: Proporcionar funciones globales para verificación de permisos
 * Estas funciones se cargan automáticamente vía Composer autoload files
 * 
 * Uso:
 *   hasPermission('clients')
 *   canView('products')
 *   canEdit('clients')
 *   canDo('products', 'taxes')
 * 
 * @package App\Helpers
 */

use App\Helpers\PermissionHelper;

if (!function_exists('hasPermission')) {
    /**
     * Verificar si el usuario tiene un permiso específico.
     * 
     * @param string $modulo
     * @param string|null $codigo
     * @return bool
     */
    function hasPermission(string $modulo, ?string $codigo = null): bool
    {
        return PermissionHelper::hasPermission($modulo, $codigo);
    }
}

if (!function_exists('canView')) {
    /**
     * Verificar si el usuario puede ver un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    function canView(string $modulo): bool
    {
        return PermissionHelper::canView($modulo);
    }
}

if (!function_exists('canCreate')) {
    /**
     * Verificar si el usuario puede crear en un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    function canCreate(string $modulo): bool
    {
        return PermissionHelper::canCreate($modulo);
    }
}

if (!function_exists('canEdit')) {
    /**
     * Verificar si el usuario puede editar en un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    function canEdit(string $modulo): bool
    {
        return PermissionHelper::canEdit($modulo);
    }
}

if (!function_exists('canDelete')) {
    /**
     * Verificar si el usuario puede eliminar en un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    function canDelete(string $modulo): bool
    {
        return PermissionHelper::canDelete($modulo);
    }
}

if (!function_exists('canDo')) {
    /**
     * Verificar si el usuario tiene un permiso granular específico.
     * 
     * @param string $modulo
     * @param string $permisoGranular
     * @return bool
     */
    function canDo(string $modulo, string $permisoGranular): bool
    {
        return PermissionHelper::canDo($modulo, $permisoGranular);
    }
}

if (!function_exists('hasFullAccess')) {
    /**
     * Verificar si el usuario tiene acceso completo a un módulo.
     * 
     * @param string $modulo
     * @return bool
     */
    function hasFullAccess(string $modulo): bool
    {
        return PermissionHelper::hasFullAccess($modulo);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Verificar si el usuario es administrador.
     * 
     * @return bool
     */
    function isAdmin(): bool
    {
        return PermissionHelper::isAdmin();
    }
}

if (!function_exists('getUserPermissions')) {
    /**
     * Obtener todos los permisos del usuario.
     * 
     * @return \Illuminate\Support\Collection
     */
    function getUserPermissions(): \Illuminate\Support\Collection
    {
        return PermissionHelper::getUserPermissions();
    }
}

if (!function_exists('getPermisosPorModulo')) {
    /**
     * Obtener permisos del usuario agrupados por módulo.
     * 
     * @return array
     */
    function getPermisosPorModulo(): array
    {
        return PermissionHelper::getPermisosPorModulo();
    }
}

if (!function_exists('hasAnyPermission')) {
    /**
     * Verificar si el usuario tiene ALGUNO de los permisos listados.
     * 
     * @param array $permisos
     * @return bool
     */
    function hasAnyPermission(array $permisos): bool
    {
        return PermissionHelper::hasAnyPermission($permisos);
    }
}

if (!function_exists('hasAllPermissions')) {
    /**
     * Verificar si el usuario tiene TODOS los permisos listados.
     * 
     * @param array $permisos
     * @return bool
     */
    function hasAllPermissions(array $permisos): bool
    {
        return PermissionHelper::hasAllPermissions($permisos);
    }
}

if (!function_exists('abortUnlessHasPermission')) {
    /**
     * Abortar si el usuario no tiene el permiso especificado.
     * 
     * @param string $modulo
     * @param string|null $codigo
     * @param string|null $message
     * @return void
     */
    function abortUnlessHasPermission(
        string $modulo,
        ?string $codigo = null,
        ?string $message = null
    ): void {
        PermissionHelper::abortUnlessHasPermission($modulo, $codigo, $message);
    }
}

if (!function_exists('abortUnlessCanView')) {
    /**
     * Abortar si el usuario no puede ver el módulo.
     * 
     * @param string $modulo
     * @return void
     */
    function abortUnlessCanView(string $modulo): void
    {
        PermissionHelper::abortUnlessCanView($modulo);
    }
}

if (!function_exists('abortUnlessCanEdit')) {
    /**
     * Abortar si el usuario no puede editar el módulo.
     * 
     * @param string $modulo
     * @return void
     */
    function abortUnlessCanEdit(string $modulo): void
    {
        PermissionHelper::abortUnlessCanEdit($modulo);
    }
}

if (!function_exists('getCurrentUserName')) {
    /**
     * Obtener el nombre del usuario actual.
     * 
     * @return string
     */
    function getCurrentUserName(): string
    {
        return PermissionHelper::getCurrentUserName();
    }
}

if (!function_exists('isCurrentUser')) {
    /**
     * Verificar si el usuario actual es el mismo que el especificado.
     * 
     * @param int $usuarioId
     * @return bool
     */
    function isCurrentUser(int $usuarioId): bool
    {
        return PermissionHelper::isCurrentUser($usuarioId);
    }
}
