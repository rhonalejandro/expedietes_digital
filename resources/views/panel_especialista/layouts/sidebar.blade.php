@php
    $esp                = auth('especialista')->user();
    $empresa            = \App\Models\Empresa::find(1);
    $logoRectangularUrl = $empresa?->logo_rectangular
        ? asset('storage/' . $empresa->logo_rectangular)
        : asset('assets/images/logo/1.png');
    $logoRedondoUrl     = $empresa?->logo
        ? asset('storage/' . $empresa->logo)
        : asset('assets/images/logo/favicon.png');
@endphp

<nav class="semi-nav">
    <div class="app-logo">
        {{-- Logo (mismo comportamiento expandido/colapsado del admin) --}}
        <a class="logo d-inline-block" href="{{ route('panel.agenda') }}">
            <img class="sidebar-logo-rect" alt="Global Feet" src="{{ $logoRectangularUrl }}">
            <img class="sidebar-logo-round" alt="GF" src="{{ $logoRedondoUrl }}">
        </a>

        {{-- Toggle expandir/colapsar --}}
        <span class="bg-light-primary toggle-semi-nav d-flex-center">
            <i class="ti ti-chevron-right"></i>
        </span>

        {{-- Perfil del especialista --}}
        <div class="d-flex align-items-center nav-profile p-3">
            <div class="flex-grow-1 ps-2">
                <h6 class="text-primary mb-0">
                    {{ $esp->tratamiento }} {{ $esp->persona->nombre ?? '' }}
                </h6>
                <p class="text-muted f-s-12 mb-0">
                    {{ $esp->especialidad ?? $esp->profesion ?? 'Especialista' }}
                </p>
            </div>

            <div class="dropdown profile-menu-dropdown">
                <a aria-expanded="false" data-bs-auto-close="true" data-bs-toggle="dropdown" role="button">
                    <i class="ti ti-settings fs-5"></i>
                </a>
                <ul class="dropdown-menu">
                    <li class="dropdown-item">
                        <form method="POST" action="{{ route('panel.logout') }}">
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

            <li class="menu-title"><span>Mi Panel</span></li>

            <li class="{{ request()->routeIs('panel.agenda') ? 'active' : '' }}">
                <a href="{{ route('panel.agenda') }}" title="Mi Agenda">
                    <i class="ti ti-calendar-event nav-icon"></i>
                    <span class="nav-label">Mi Agenda</span>
                </a>
            </li>

            {{-- Más módulos se agregan por fase --}}

        </ul>
    </div>
</nav>
