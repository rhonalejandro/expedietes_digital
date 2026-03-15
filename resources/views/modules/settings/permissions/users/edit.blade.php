{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Vista: users/edit.blade.php
--}}

@extends('layouts.admin.master')

@section('title', 'Asignar Permisos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/modules/settings/permissions/css/permissions.module.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container-fluid">
    @include('modules.settings.permissions._partials.alerts')

    <form method="POST" action="{{ route('settings.permissions.users.update', $usuario->id) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            @include('modules.settings.permissions._partials.sidebar')

            <div class="col-lg-9 col-xl-10">
                <!-- Info Usuario -->
                <div class="card settings-card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 h-60 w-60 d-flex-center b-r-50 me-3">
                                <i class="ti ti-user f-s-32 text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $usuario->persona->nombre ?? $usuario->nombre }}</h5>
                                <p class="text-muted mb-0">{{ $usuario->email }}</p>
                                @if($usuario->roles->isNotEmpty())
                                    <div class="mt-2">
                                        @foreach($usuario->roles as $rol)
                                            <span class="badge bg-primary me-1">{{ $rol->nombre }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asignación Rápida con Plantilla -->
                <div class="card settings-card mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0"><i class="ti ti-bolt me-2"></i>Asignación Rápida</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Seleccionar Plantilla</label>
                                <select name="plantilla_id" class="form-select">
                                    <option value="">-- Sin plantilla --</option>
                                    @foreach($plantillas as $plantilla)
                                        <option value="{{ $plantilla->id }}" 
                                                {{ in_array($plantilla->id, $permisosActuales ?? []) ? 'selected' : '' }}>
                                            {{ $plantilla->nombre }} ({{ $plantilla->permisos->count() }} permisos)
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Selecciona una plantilla para asignar permisos automáticamente</small>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary" onclick="applyTemplate()">
                                    <i class="ti ti-check me-1"></i> Aplicar Plantilla
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permisos Detallados -->
                <div class="card settings-card mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0"><i class="ti ti-shield me-2"></i>Permisos Detallados</h5>
                    </div>
                    <div class="card-body">
                        @foreach($permisos as $modulo => $listaPermisos)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <span class="badge bg-primary bg-opacity-10 text-primary me-2">
                                            {{ ucfirst($modulo) }}
                                        </span>
                                    </h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               onchange="toggleModulo('{{ $modulo }}', this.checked)">
                                        <label class="form-check-label small">Seleccionar todos</label>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    @foreach($listaPermisos as $permiso)
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input class="form-check-input permiso-checkbox" type="checkbox" 
                                                       name="permisos[]" value="{{ $permiso['id'] }}"
                                                       data-modulo="{{ $modulo }}"
                                                       {{ in_array($permiso['id'], $permisosActuales) ? 'checked' : '' }}
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
                    <a href="{{ route('settings.permissions.users.index') }}" class="btn btn-light">
                        <i class="ti ti-x me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i> Guardar Permisos
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleModulo(modulo, checked) {
    document.querySelectorAll(`.permiso-checkbox[data-modulo="${modulo}"]`).forEach(cb => {
        cb.checked = checked;
    });
}

function applyTemplate() {
    const plantillaId = document.querySelector('[name="plantilla_id"]').value;
    if (!plantillaId) {
        alert('Selecciona una plantilla primero');
        return;
    }
    
    // Submit form para aplicar plantilla
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('settings.permissions.users.assign-template') }}";
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = "{{ csrf_token() }}";
    
    const usuarioId = document.createElement('input');
    usuarioId.type = 'hidden';
    usuarioId.name = 'usuario_id';
    usuarioId.value = {{ $usuario->id }};
    
    const plantillaIdInput = document.createElement('input');
    plantillaIdInput.type = 'hidden';
    plantillaIdInput.name = 'plantilla_id';
    plantillaIdInput.value = plantillaId;
    
    form.appendChild(csrf);
    form.appendChild(usuarioId);
    form.appendChild(plantillaIdInput);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
