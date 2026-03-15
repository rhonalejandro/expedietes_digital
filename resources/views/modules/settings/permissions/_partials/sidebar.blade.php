{{--
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Partial: sidebar.blade.php (< 50 líneas)
--}}

<div class="col-lg-3 col-xl-2 mb-4">
    <div class="card settings-card">
        <div class="card-body p-3">
            <div class="text-center mb-3">
                <div class="bg-primary bg-opacity-10 h-70 w-70 d-flex-center b-r-10 m-auto mb-2">
                    <i class="ti ti-shield-lock f-s-32 text-primary"></i>
                </div>
                <h6 class="mb-1">Permisos</h6>
                <p class="text-muted small mb-0">Gestión de acceso</p>
            </div>

            <ul class="nav flex-column settings-nav">
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('settings.permissions.index') ? 'active' : '' }}" 
                       href="{{ route('settings.permissions.index') }}">
                        <i class="ti ti-shield"></i> Todos los permisos
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('settings.permissions.create') ? 'active' : '' }}" 
                       href="{{ route('settings.permissions.create') }}">
                        <i class="ti ti-plus"></i> Nuevo Permiso
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('settings.permissions.templates.*') ? 'active' : '' }}" 
                       href="{{ route('settings.permissions.templates.index') }}">
                        <i class="ti ti-copy"></i> Plantillas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings.permissions.users.*') ? 'active' : '' }}" 
                       href="{{ route('settings.permissions.users.index') }}">
                        <i class="ti ti-users"></i> Asignar a Usuarios
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
