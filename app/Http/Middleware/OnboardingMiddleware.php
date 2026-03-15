<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnboardingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /**
     * Verifica si existe al menos una empresa y un usuario administrador.
     * Si no existen, redirige al onboarding inicial.
     * Permite acceso normal si ya están configurados.
     *
     * @context Ver lineamientos en Contexto.md (internacionalización, modularidad, seguridad)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Evitar bucle infinito en la ruta de onboarding
        if ($request->is('onboarding*')) {
            return $next($request);
        }

        // Verificar existencia de empresa y usuario admin
        $empresaExiste = \App\Models\Empresa::query()->exists();
        $usuarioAdminExiste = \App\Models\Usuario::whereHas('roles', function($q) {
            $q->where('nombre', 'admin')->orWhere('nombre', 'superadmin');
        })->exists();

        if (!$empresaExiste || !$usuarioAdminExiste) {
            return redirect()->route('onboarding.index');
        }

        return $next($request);
    }
}
