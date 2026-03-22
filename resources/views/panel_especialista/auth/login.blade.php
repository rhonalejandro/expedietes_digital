@extends('layouts.auth.master')

@section('title', 'Panel Especialista — Acceso')

@section('content')
<div class="">
    <main class="w-100 p-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 p-0">
                    <div class="login-form-container">
                        <div class="mb-4">
                            <a class="logo" href="{{ route('panel.login') }}">
                                @php
                                    $empresa = \App\Models\Empresa::find(1);
                                    $logoUrl = $empresa && $empresa->logo
                                        ? asset('storage/' . $empresa->logo)
                                        : asset('assets/images/logo/3.png');
                                @endphp
                                <img alt="Panel Especialista" src="{{ $logoUrl }}" class="" style="width: 72px; filter: brightness(1);">
                            </a>
                        </div>
                        <div class="form_container">
                            <form class="app-form" method="POST" action="{{ route('panel.login.submit') }}">
                                @csrf

                                <div class="mb-3 text-center">
                                    <h3>Panel Especialista</h3>
                                    <p class="f-s-12 text-secondary">Ingresa tus credenciales para acceder a tu agenda clínica</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="email">Correo electrónico</label>
                                    <input class="form-control @error('email') is-invalid @enderror"
                                           type="email"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus>
                                    <div class="form-text text">Ingresa tu correo electrónico</div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password">Contraseña</label>
                                    <input class="form-control @error('password') is-invalid @enderror"
                                           type="password"
                                           id="password"
                                           name="password"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 form-check">
                                    <input class="form-check-input" id="remember" name="remember" type="checkbox">
                                    <label class="form-check-label" for="remember">Recordarme</label>
                                </div>

                                <div>
                                    <button class="btn btn-primary w-100" type="submit">Continuar</button>
                                </div>

                                <div class="text-center mt-2">
                                    <a class="text-secondary text-decoration-underline" href="{{ route('login') }}">
                                        ¿Eres administrador? Ingresa aquí
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
