<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

/**
 * DeveloperAuthController
 *
 * Maneja la autenticación TOTP para el Developer Panel.
 * Una vez autenticado, la sesión dura config('developer.session_hours') horas.
 */
class DeveloperAuthController extends Controller
{
    // ── Formulario de login ───────────────────────────────────────────────────

    public function showLogin()
    {
        if (session('developer_authenticated')) {
            return redirect()->route('developer.modulos.index');
        }

        return view('developer.auth.login');
    }

    // ── Verificar código TOTP ─────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'El código TOTP es obligatorio.',
            'code.digits'   => 'El código debe tener exactamente 6 dígitos.',
        ]);

        $secret = config('developer.totp_secret');

        if (!$secret) {
            return back()->with('error', 'El Developer Panel no está configurado. Ejecuta php artisan developer:setup-totp');
        }

        $google2fa = new Google2FA();
        $window    = (int) config('developer.totp_window', 1);

        $valid = $google2fa->verifyKey($secret, $request->code, $window);

        if (!$valid) {
            return back()->with('error', 'Código incorrecto. Verifica la hora de tu dispositivo y vuelve a intentarlo.');
        }

        session([
            'developer_authenticated'    => true,
            'developer_authenticated_at' => now(),
        ]);

        return redirect()->route('developer.modulos.index');
    }

    // ── Cerrar sesión developer ───────────────────────────────────────────────

    public function logout()
    {
        session()->forget(['developer_authenticated', 'developer_authenticated_at']);

        return redirect()->route('developer.login')->with('success', 'Sesión cerrada correctamente.');
    }
}
