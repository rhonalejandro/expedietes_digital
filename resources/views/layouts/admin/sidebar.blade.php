@php
    $empresa            = \App\Models\Empresa::find(1);
    $logoRectangularUrl = $empresa?->logo_rectangular
        ? asset('storage/' . $empresa->logo_rectangular)
        : asset('assets/images/logo/1.png');
    $logoRedondoUrl     = $empresa?->logo
        ? asset('storage/' . $empresa->logo)
        : asset('assets/images/logo/favicon.png');
@endphp

<!-- Menu Navigation starts -->
<nav class="semi-nav">
    <div class="app-logo">
        <a class="logo d-inline-block" href="{{ route('dashboard') }}">
            {{-- Logo rectangular: visible cuando el sidebar está expandido --}}
            <img class="sidebar-logo-rect" alt="Global Feet" src="{{ $logoRectangularUrl }}">
            {{-- Logo redondo: visible cuando el sidebar está colapsado --}}
            <img class="sidebar-logo-round" alt="GF" src="{{ $logoRedondoUrl }}">
        </a>

        <span class="bg-light-primary toggle-semi-nav d-flex-center">
            <i class="ti ti-chevron-right"></i>
        </span>

        <div class="d-flex align-items-center nav-profile p-3">
            <div class="flex-grow-1 ps-2">
                <h6 class="text-primary mb-0">{{ auth()->user()->persona->nombre ?? 'Usuario' }}</h6>
                <p class="text-muted f-s-12 mb-0">Administrador</p>
            </div>

            <div class="dropdown profile-menu-dropdown">
                <a aria-expanded="false" data-bs-auto-close="true" data-bs-placement="top" data-bs-toggle="dropdown" role="button">
                    <i class="ti ti-settings fs-5"></i>
                </a>
                <ul class="dropdown-menu">
                    <li class="dropdown-item">
                        <a class="f-w-500" href="{{ route('settings.index') }}">
                            <i class="ti ti-user pe-1 f-s-20"></i> Configuración
                        </a>
                    </li>
                    <li class="app-divider-v dotted py-1"></li>
                    <li class="dropdown-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="mb-0 text-danger border-0 bg-transparent w-100 text-start">
                                <i class="ti ti-logout pe-1 f-s-20"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="app-nav" id="app-simple-bar">
        <ul class="main-nav p-0 mt-2">

            <li class="menu-title"><span>Menú Principal</span></li>

            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" title="Dashboard">
                    <i class="ti ti-home nav-icon"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>

            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#empresas" title="Empresas">
                    <i class="ti ti-building-store nav-icon"></i>
                    <span class="nav-label">Empresas</span>
                </a>
            </li>

            <li class="menu-title"><span>Clínica</span></li>

            <li class="{{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                <a href="{{ route('pacientes.index') }}" title="Pacientes">
                    <i class="ti ti-users nav-icon"></i>
                    <span class="nav-label">Pacientes</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('especialistas.*') ? 'active' : '' }}">
                <a href="{{ route('especialistas.index') }}" title="Especialistas">
                    <i class="ti ti-stethoscope nav-icon"></i>
                    <span class="nav-label">Especialistas</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('citas.*') ? 'active' : '' }}">
                <a href="{{ route('citas.index') }}" title="Citas">
                    <i class="ti ti-calendar-event nav-icon"></i>
                    <span class="nav-label">Citas</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('servicios.*') ? 'active' : '' }}">
                <a href="{{ route('servicios.index') }}" title="Servicios">
                    <i class="ti ti-clipboard-list nav-icon"></i>
                    <span class="nav-label">Servicios</span>
                </a>
            </li>

            <li class="menu-title"><span>Expedientes</span></li>

            <li class="{{ request()->routeIs('casos.*') ? 'active' : '' }}">
                <a aria-expanded="false" data-bs-toggle="collapse" href="#casos" title="Casos">
                    <i class="ti ti-folders nav-icon"></i>
                    <span class="nav-label">Casos</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('expedientes.*') ? 'active' : '' }}">
                <a aria-expanded="false" data-bs-toggle="collapse" href="#expedientes" title="Expedientes">
                    <i class="ti ti-file-description nav-icon"></i>
                    <span class="nav-label">Expedientes</span>
                </a>
            </li>

        </ul>
    </div>
</nav>
<!-- Menu Navigation ends -->
