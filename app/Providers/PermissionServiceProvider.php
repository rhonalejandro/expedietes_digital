<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Provider: PermissionServiceProvider
 * 
 * Propósito: Registrar el sistema de permisos en la aplicación
 * - Registra directivas Blade para verificación de permisos
 * - Registra Gates para autorización
 * - Publica el config del sistema de permisos
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo registra componentes de permisos
 * - Open/Closed: Extiende la aplicación sin modificar código existente
 * - Dependency Inversion: Usa el contenedor de servicios
 * - Documentación exhaustiva
 * 
 * @package App\Providers
 */

namespace App\Providers;

use App\Helpers\PermissionHelper;
use App\Models\Permiso;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

/**
 * Class PermissionServiceProvider
 * 
 * Proveedor de servicios para el sistema de permisos.
 * 
 * @package App\Providers
 */
class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Registro de servicios del sistema de permisos.
     * 
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap de servicios del sistema de permisos.
     * 
     * @return void
     */
    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->registerGates();
        $this->registerMacroHelpers();
    }

    /**
     * Registrar directivas Blade para verificación de permisos.
     * 
     * Estas directivas permiten usar sintaxis limpia en vistas Blade:
     *   @canView('clients') ... @endcanView
     *   @canEdit('products') ... @endcanEdit
     *   @canDo('products', 'taxes') ... @endcanDo
     * 
     * @return void
     */
    protected function registerBladeDirectives(): void
    {
        /**
         * Verificar si puede ver un módulo
         * @canView('clients')
         */
        Blade::if('canView', function (string $modulo): bool {
            return canView($modulo);
        });

        /**
         * Verificar si puede crear en un módulo
         * @canCreate('clients')
         */
        Blade::if('canCreate', function (string $modulo): bool {
            return canCreate($modulo);
        });

        /**
         * Verificar si puede editar un módulo
         * @canEdit('clients')
         */
        Blade::if('canEdit', function (string $modulo): bool {
            return canEdit($modulo);
        });

        /**
         * Verificar si puede eliminar en un módulo
         * @canDelete('clients')
         */
        Blade::if('canDelete', function (string $modulo): bool {
            return canDelete($modulo);
        });

        /**
         * Verificar permiso granular específico
         * @canDo('products', 'taxes')
         */
        Blade::if('canDo', function (string $modulo, string $permiso): bool {
            return canDo($modulo, $permiso);
        });

        /**
         * Verificar si tiene un permiso específico
         * @hasPermission('clients', 'view')
         */
        Blade::if('hasPermission', function (string $modulo, ?string $codigo = null): bool {
            return hasPermission($modulo, $codigo);
        });

        /**
         * Verificar si tiene acceso completo a un módulo
         * @hasFullAccess('clients')
         */
        Blade::if('hasFullAccess', function (string $modulo): bool {
            return hasFullAccess($modulo);
        });

        /**
         * Verificar si es administrador
         * @isAdmin
         */
        Blade::if('isAdmin', function (): bool {
            return isAdmin();
        });

        /**
         * Verificar si tiene ALGUNO de los permisos listados
         * @hasAnyPermission([['clients', 'view'], ['products', 'view']])
         */
        Blade::if('hasAnyPermission', function (array $permisos): bool {
            return hasAnyPermission($permisos);
        });

        /**
         * Verificar si tiene TODOS los permisos listados
         * @hasAllPermissions([['clients', 'view'], ['clients', 'edit']])
         */
        Blade::if('hasAllPermissions', function (array $permisos): bool {
            return hasAllPermissions($permisos);
        });
    }

    /**
     * Registrar Gates para autorización.
     * 
     * Los Gates permiten verificar permisos usando la fachada Gate:
     *   Gate::allows('view-clients')
     *   Gate::denies('edit-products')
     * 
     * @return void
     */
    protected function registerGates(): void
    {
        // Gate dinámico para ver módulos
        Gate::define('view-module', function ($user, string $modulo) {
            return $user->canView($modulo);
        });

        // Gate dinámico para crear en módulos
        Gate::define('create-module', function ($user, string $modulo) {
            return $user->canCreate($modulo);
        });

        // Gate dinámico para editar módulos
        Gate::define('edit-module', function ($user, string $modulo) {
            return $user->canEdit($modulo);
        });

        // Gate dinámico para eliminar en módulos
        Gate::define('delete-module', function ($user, string $modulo) {
            return $user->canDelete($modulo);
        });

        // Gate dinámico para permisos granulares
        Gate::define('granular-permission', function ($user, string $modulo, string $permiso) {
            return $user->canDo($modulo, $permiso);
        });
    }

    /**
     * Registrar helper macros para clases.
     * 
     * @return void
     */
    protected function registerMacroHelpers(): void
    {
        // Macro para verificar permisos en colecciones de usuarios
        // Ej: $usuarios->filterByPermission('clients', 'view')
        // Nota: Esto se puede implementar si se necesita en el futuro
    }

    /**
     * Obtener los servicios provistos por este provider.
     * 
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [];
    }
}
