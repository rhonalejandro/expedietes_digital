<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware DeveloperAccess
 *
 * Protege las rutas del Developer Panel (/developer/*).
 * Requiere autenticación TOTP previa (sesión developer_authenticated).
 *
 * La sesión expira a las DEVELOPER_SESSION_HOURS horas (default: 8).
 * Si el secret TOTP no está configurado, redirige a instrucciones de setup.
 */
class DeveloperAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar que el secret esté configurado
        if (!config('developer.totp_secret')) {
            return response('<h2>Developer Panel no configurado.</h2>'
                . '<p>Ejecuta: <code>php artisan developer:setup-totp</code></p>', 503);
        }

        // Verificar sesión activa
        if (!session('developer_authenticated')) {
            return redirect()->route('developer.login');
        }

        // Verificar expiración de sesión (horas configurables)
        $sessionHours = (int) config('developer.session_hours', 8);
        $authenticatedAt = session('developer_authenticated_at');

        if (!$authenticatedAt || now()->diffInHours($authenticatedAt) >= $sessionHours) {
            session()->forget(['developer_authenticated', 'developer_authenticated_at']);
            return redirect()->route('developer.login')->with('error', 'Sesión expirada. Ingresa tu código TOTP nuevamente.');
        }

        return $next($request);
    }
}
