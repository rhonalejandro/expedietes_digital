<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Especialista') — Expediente Digital</title>

    <link rel="icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('assets/vendor/animation/animate.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" crossorigin href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/simplebar/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/style-Cuxwy5N_.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-font-size.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/panel_especialista/css/panel.css') }}?v={{ time() }}">

    @stack('styles')
</head>
<body>
<div class="app-wrapper">

    {{-- Sidebar del especialista --}}
    @include('panel_especialista.layouts.sidebar')

    <div class="app-content">
        {{-- Header --}}
        @include('panel_especialista.layouts.header')

        {{-- Contenido --}}
        <main>
            @if(session('success'))
                <div class="container-fluid pt-3 pb-0">
                    <div class="alert alert-success d-flex align-items-center gap-2 mb-0">
                        <i class="ti ti-circle-check fs-5"></i> {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="container-fluid pt-3 pb-0">
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-0">
                        <i class="ti ti-alert-circle fs-5"></i> {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        @include('layouts.admin.footer')
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.6.3.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/simplebar/simplebar.js') }}"></script>
<script src="{{ asset('assets/vendor/phosphor/phosphor.js') }}"></script>
{{-- customizer.js y script.js son del panel admin (rutas relativas incorrectas aquí) --}}
<script>
/* Inicializar Simplebar en el sidebar para el panel especialista */
document.addEventListener('DOMContentLoaded', function () {
    var sidebarEl = document.querySelector('.semi-nav [data-simplebar]');
    if (sidebarEl && typeof SimpleBar !== 'undefined') new SimpleBar(sidebarEl);

    /* Toggle sidebar (mismo comportamiento que script.js del admin) */
    var toggleBtn = document.querySelector('.toggle-semi-nav');
    var wrapper   = document.querySelector('.app-wrapper');
    if (toggleBtn && wrapper) {
        toggleBtn.addEventListener('click', function () {
            wrapper.classList.toggle('toggle-sidebar');
        });
    }
});
</script>

@stack('scripts')

<script>
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
    } else {
        document.exitFullscreen().catch(() => {});
    }
}
document.addEventListener('fullscreenchange', () => {
    const icon = document.getElementById('fs-icon');
    if (!icon) return;
    icon.className = document.fullscreenElement ? 'ti ti-minimize' : 'ti ti-maximize';
});
</script>
</body>
</html>
