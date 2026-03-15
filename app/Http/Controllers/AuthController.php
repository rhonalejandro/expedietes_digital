<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Autenticación
 * 
 * Maneja el login, logout y registro de usuarios del sistema.
 */
class AuthController extends Controller
{
    /**
     * Mostrar el formulario de login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
        // Si hay error de vista no encontrada, verificar:
        // 1. Ejecutar: php artisan view:clear (desde Windows CMD)
        // 2. Reiniciar Laragon completamente
        // 3. Verificar que resources/views/auth/login.blade.php existe
    }

    /**
     * Procesar el login del usuario.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validar credenciales
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intentar autenticar
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirigir al dashboard o ruta intentada
            return redirect()->intended(route('dashboard'));
        }

        // Autenticación fallida
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.',
        ])->onlyInput('email');
    }

    /**
     * Mostrar el formulario de registro.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Procesar el registro de un nuevo usuario.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:usuarios,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // TODO: Implementar registro completo con Persona y Usuario
        // Por ahora solo retornamos un error de no implementado
        return back()->withErrors([
            'email' => 'El registro está temporalmente deshabilitado.',
        ]);
    }

    /**
     * Cerrar sesión del usuario.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
