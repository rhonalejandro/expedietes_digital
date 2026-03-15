{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Vista: templates/create.blade.php
--}}

@extends('layouts.admin.master')

@section('title', 'Crear Plantilla')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/settings/permissions/css/permissions.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">
    @include('modules.settings.permissions._partials.alerts')

    <form method="POST" action="{{ route('settings.permissions.templates.store') }}">
        @csrf
        
        <div class="row">
            @include('modules.settings.permissions._partials.sidebar')

            <div class="col-lg-9 col-xl-10">
                <div class="card settings-card mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0"><i class="ti ti-copy me-2"></i>Nueva Plantilla</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Color</label>
                                <input type="color" name="color" class="form-control form-control-color"
                                       value="{{ old('color', '#667eea') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="3">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Icono</label>
                                <select name="icono" class="form-select">
                                    <option value="ti ti-shield">Escudo</option>
                                    <option value="ti ti-user">Usuario</option>
                                    <option value="ti ti-users">Usuarios</option>
                                    <option value="ti ti-reception">Recepción</option>
                                    <option value="ti ti-user-md">Doctor</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="es_sistema" value="1">
                                    <label class="form-check-label">Es del sistema</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="es_activa" value="1" checked>
                                    <label class="form-check-label">Activa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card settings-card mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0"><i class="ti ti-shield me-2"></i>Seleccionar Permisos</h5>
                    </div>
                    <div class="card-body">
                        @foreach($permisos as $modulo => $listaPermisos)
                            <div class="mb-4">
                                <h6 class="mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary me-2">
                                        {{ ucfirst($modulo) }}
                                    </span>
                                </h6>
                                <div class="row g-2">
                                    @foreach($listaPermisos as $permiso)
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="permisos[]" value="{{ $permiso['id'] }}"
                                                       id="permiso_{{ $permiso['id'] }}">
                                                <label class="form-check-label" for="permiso_{{ $permiso['id'] }}">
                                                    {{ $permiso['nombre'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('settings.permissions.templates.index') }}" class="btn btn-light">
                        <i class="ti ti-x me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i> Guardar Plantilla
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
