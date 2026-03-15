<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Middleware: CheckModuleAccess
 * 
 * Propósito: Verificar acceso básico a un módulo (permiso view)
 * Más simple que CheckPermission, solo verifica si puede ver el módulo
 * 
 * Uso en rutas:
 *   Route::middleware(['auth', 'module.access:clients'])->group(...)
 *   Route::get('/doctors', ...)->middleware('module.access:doctors');
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo verifica acceso a módulos
 * - DRY: Reutiliza la lógica de hasPermission
 * - Documentación exhaustiva
 * 
 * @package App\Http\Middleware
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckModuleAccess
 * 
 * Middleware para verificar acceso básico a módulos.
 * 
 * @package App\Http\Middleware
 */
class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $modulo  Nombre del módulo a verificar
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @throws \Illuminate\Auth\AuthenticationException Si no está autenticado
     * @throws \Illuminate\Auth\Access\AuthorizationException Si no tiene acceso al módulo
     */
    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        // Verificar autenticación
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder.');
        }

        // Verificar si el usuario tiene acceso al módulo (al menos permiso view)
        $user = auth()->user();
        
        if (!$user->canView($modulo)) {
            // Log para auditoría
            \Log::warning('Intento de acceso a módulo sin permiso view', [
                'usuario_id' => $user->id,
                'modulo' => $modulo,
                'ruta' => $request->path(),
                'ip' => $request->ip(),
            ]);

            // Manejar según tipo de request
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver este módulo.',
                    'error' => 'Forbidden',
                ], 403);
            }

            // Request normal - mostrar vista de error 403
            abort(403, 'No tienes permiso para acceder a este módulo.');
        }

        // Acceso verificado - continuar con la request
        return $next($request);
    }
}
