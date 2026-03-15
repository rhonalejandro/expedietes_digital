@extends('layouts.auth.master')

@section('title', 'Registro')

@section('content')
<div class="">
    <main class="w-100 p-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 p-0">
                    <div class="login-form-container">
                        <div class="mb-4">
                            <a class="logo" href="{{ route('login') }}">
                                <img alt="Expediente Digital" src="{{ asset('assets/images/logo/3.png') }}">
                            </a>
                        </div>
                        <div class="form_container">
                            <form class="app-form" method="POST" action="{{ route('register') }}">
                                @csrf
                                
                                <div class="mb-3 text-center">
                                    <h3>Crear Cuenta</h3>
                                    <p class="f-s-12 text-secondary">Completa el formulario para registrarte</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="nombre">Nombre</label>
                                            <input class="form-control @error('nombre') is-invalid @enderror" 
                                                   type="text" 
                                                   id="nombre" 
                                                   name="nombre" 
                                                   value="{{ old('nombre') }}" 
                                                   required>
                                            @error('nombre')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="apellido">Apellido</label>
                                            <input class="form-control @error('apellido') is-invalid @enderror" 
                                                   type="text" 
                                                   id="apellido" 
                                                   name="apellido" 
                                                   value="{{ old('apellido') }}" 
                                                   required>
                                            @error('apellido')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="email">Correo electrónico</label>
                                    <input class="form-control @error('email') is-invalid @enderror" 
                                           type="email" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required>
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
                                
                                <div class="mb-3">
                                    <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                                    <input class="form-control" 
                                           type="password" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                </div>
                                
                                <div>
                                    <button class="btn btn-primary w-100" type="submit">Registrarse</button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <a class="text-secondary" href="{{ route('login') }}">
                                        ¿Ya tienes cuenta? Inicia sesión
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
