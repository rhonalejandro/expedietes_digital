{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Componente: template-card.blade.php
--}}

@props(['plantilla'])

<div class="col-md-6 col-xl-4">
    <div class="template-card" style="border-left: 4px solid {{ $plantilla->color }};">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="template-icon" style="background: {{ $plantilla->color }}20; color: {{ $plantilla->color }};">
                <i class="{{ $plantilla->icono ?? 'ti ti-shield' }}"></i>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('settings.permissions.templates.edit', $plantilla->id) }}">
                            <i class="ti ti-edit me-2"></i>Editar
                        </a>
                    </li>
                    @if(!$plantilla->es_sistema)
                        <li>
                            <button class="dropdown-item text-danger" onclick="deleteTemplate({{ $plantilla->id }})">
                                <i class="ti ti-trash me-2"></i>Eliminar
                            </button>
                        </li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button class="dropdown-item" onclick="toggleTemplateStatus({{ $plantilla->id }})">
                            <i class="ti ti-{{ $plantilla->es_activa ? 'eye-off' : 'eye' }} me-2"></i>
                            {{ $plantilla->es_activa ? 'Desactivar' : 'Activar' }}
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <h6 class="mb-2">{{ $plantilla->nombre }}</h6>
        <p class="text-muted small mb-3">{{ Str::limit($plantilla->descripcion, 80) ?? 'Sin descripción' }}</p>

        <div class="d-flex justify-content-between align-items-center">
            <div class="template-stats">
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="ti ti-shield me-1"></i>{{ $plantilla->permisos_count }} permisos
                </span>
            </div>
            
            <span class="badge {{ $plantilla->es_activa ? 'bg-success' : 'bg-secondary' }}">
                {{ $plantilla->es_activa ? 'Activa' : 'Inactiva' }}
            </span>
        </div>

        @if($plantilla->es_sistema)
            <div class="mt-3">
                <span class="badge bg-warning text-dark">
                    <i class="ti ti-lock me-1"></i> Sistema
                </span>
            </div>
        @endif

        <div class="mt-3 pt-3 border-top">
            <a href="{{ route('settings.permissions.users.edit', $plantilla->id) }}" class="btn btn-sm btn-outline-primary w-100">
                <i class="ti ti-user me-1"></i> Asignar a Usuario
            </a>
        </div>
    </div>
</div>
