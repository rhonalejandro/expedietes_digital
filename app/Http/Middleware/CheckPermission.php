<?php

/**
 * Desarrollo - Permisos de Usuarios - 2026-02-18
 * 
 * Middleware: CheckPermission
 * 
 * Propósito: Verificar permisos antes de permitir acceso a rutas
 * Se usa en rutas que requieren permisos específicos
 * 
 * Uso en rutas:
 *   Route::middleware(['auth', 'permission:clients.edit'])->group(...)
 *   Route::get('/clients', ...)->middleware('permission:clients.view');
 * 
 * Principios aplicados:
 * - Single Responsibility: Solo verifica permisos
 * - Open/Closed: Extiende el middleware de Laravel sin modificar
 * - Documentación exhaustiva
 * 
 * @package App\Http\Middleware
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckPermission
 * 
 * Middleware para verificar permisos en rutas.
 * 
 * @package App\Http\Middleware
 */
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  Formato: "modulo.codigo" o solo "modulo" para cualquier permiso del módulo
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @throws \Illuminate\Auth\AuthenticationException Si no está autenticado
     * @throws \Illuminate\Auth\Access\AuthorizationException Si no tiene el permiso
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Verificar autenticación
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder.');
        }

        // Parsear el permiso (formato: "modulo.codigo" o "modulo")
        $parts = explode('.', $permission);
        $modulo = $parts[0];
        $codigo = $parts[1] ?? null;

        // Verificar si el usuario tiene el permiso
        $user = auth()->user();
        
        if (!$user->hasPermission($modulo, $codigo)) {
            // Log para auditoría (opcional)
            \Log::warning('Intento de acceso sin permiso', [
                'usuario_id' => $user->id,
                'modulo' => $modulo,
                'codigo' => $codigo,
                'ruta' => $request->path(),
                'ip' => $request->ip(),
            ]);

            // Manejar según tipo de request
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para realizar esta acción.',
                    'error' => 'Forbidden',
                ], 403);
            }

            // Request normal - mostrar vista de error 403
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Permiso verificado - continuar con la request
        return $next($request);
    }
}
