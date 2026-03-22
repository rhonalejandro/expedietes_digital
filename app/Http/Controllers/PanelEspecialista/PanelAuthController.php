<?php

namespace App\Http\Controllers\PanelEspecialista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanelAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('especialista')->check()) {
            return redirect()->route('panel.agenda');
        }

        return view('panel_especialista.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::guard('especialista')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('panel.agenda'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Correo o contraseña incorrectos.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('especialista')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('panel.login');
    }
}
