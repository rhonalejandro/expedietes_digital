<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Muestra la vista principal del onboarding.
     */
    public function index()
    {
        return view('onboarding.index');
    }

    /**
     * Procesa el registro inicial de empresa y superusuario.
     */
    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'empresa_nombre' => 'required|string|max:255',
            'empresa_tipo_identificacion' => 'required|string|max:50',
            'empresa_identificacion' => 'required|string|max:50',
            'empresa_direccion' => 'required|string|max:255',
            'empresa_telefono' => 'required|string|max:100',
            'empresa_email' => 'required|email|max:150',
            'empresa_pagina_web' => 'nullable|url|max:150',
            'empresa_redes_sociales' => 'nullable|string',
            'usuario_nombre' => 'required|string|max:255',
            'usuario_email' => 'required|email|unique:usuarios,email',
            'usuario_password' => 'required|string|min:8|confirmed',
        ]);

        // Procesar redes sociales (acepta JSON o texto plano)
        $redes = $validated['empresa_redes_sociales'] ?? null;
        if ($redes) {
            $json = null;
            if (is_string($redes)) {
                $json = json_decode($redes, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Si no es JSON válido, lo guardamos como string plano (en un array)
                    $json = array_map('trim', explode(',', $redes));
                }
            }
            $redes = json_encode($json);
        }

        // Crear empresa
        $empresa = Empresa::create([
            'nombre' => $validated['empresa_nombre'],
            'tipo_identificacion' => $validated['empresa_tipo_identificacion'],
            'identificacion' => $validated['empresa_identificacion'],
            'direccion' => $validated['empresa_direccion'],
            'telefono' => $validated['empresa_telefono'],
            'email' => $validated['empresa_email'],
            'pagina_web' => $validated['empresa_pagina_web'] ?? null,
            'redes_sociales' => $redes,
        ]);

        // Crear superusuario
        $usuario = Usuario::create([
            'nombre' => $validated['usuario_nombre'],
            'email' => $validated['usuario_email'],
            'password' => bcrypt($validated['usuario_password']),
            'empresa_id' => $empresa->id,
        ]);
        // Asignar rol superadmin
        $usuario->roles()->attach(1); // Asume que el rol 1 es superadmin

        // Login automático
        Auth::login($usuario);

        return redirect()->route('dashboard')->with('success', __('Onboarding completado.'));
    }
}
