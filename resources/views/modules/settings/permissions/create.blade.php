{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Vista: create.blade.php (< 80 líneas)
--}}

@extends('layouts.admin.master')

@section('title', 'Crear Permiso')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/settings/permissions/css/permissions.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">
    @include('modules.settings.permissions._partials.alerts')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card settings-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="ti ti-plus me-2"></i>
                        Nuevo Permiso
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.permissions.store') }}">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Módulo *</label>
                                <select name="modulo" class="form-select" required>
                                    <option value="">Seleccionar módulo</option>
                                    @foreach($modulos as $key => $label)
                                        <option value="{{ $key }}" {{ old('modulo') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('modulo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Código *</label>
                                <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
                                       value="{{ old('codigo') }}" required placeholder="ej: view, create, edit">
                                @error('codigo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre') }}" required placeholder="Nombre del permiso">
                                @error('nombre')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                          rows="3" placeholder="Descripción del permiso">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tipo *</label>
                                <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="general" {{ old('tipo') == 'general' ? 'selected' : '' }}>General (CRUD)</option>
                                    <option value="granular" {{ old('tipo') == 'granular' ? 'selected' : '' }}>Granular (Especial)</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="estado" value="1" {{ old('estado', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Activo</label>
                                </div>
                                @error('estado')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('settings.permissions.index') }}" class="btn btn-light">
                                <i class="ti ti-x me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-1"></i> Guardar Permiso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
