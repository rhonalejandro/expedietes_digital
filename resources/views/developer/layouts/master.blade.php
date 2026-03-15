<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Developer') — Dev Panel</title>

    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-font-size.css') }}">

    <style>
        :root {
            --dev-bg:       #0d1117;
            --dev-surface:  #161b22;
            --dev-border:   #30363d;
            --dev-primary:  #667eea;
            --dev-text:     #c9d1d9;
            --dev-muted:    #8b949e;
            --dev-success:  #3fb950;
            --dev-danger:   #f85149;
        }
        body          { background: var(--dev-bg); color: var(--dev-text); min-height: 100vh; }
        .dev-navbar   { background: var(--dev-surface); border-bottom: 1px solid var(--dev-border); padding: 0.65rem 1.5rem; }
        .dev-card     { background: var(--dev-surface); border: 1px solid var(--dev-border); border-radius: 8px; }
        .dev-badge    { background: rgba(102,126,234,0.15); color: var(--dev-primary); padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .dev-label    { font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--dev-muted); font-weight: 500; }
        .dev-code     { font-family: 'Courier New', monospace; background: rgba(102,126,234,.1); color: var(--dev-primary); padding: 2px 6px; border-radius: 4px; font-size: 12px; }
        .dev-table    { color: var(--dev-text); }
        .dev-table thead th { background: #1c2128; border-color: var(--dev-border); color: var(--dev-muted); font-size: 11px; text-transform: uppercase; letter-spacing: .05em; padding: .65rem 1rem; }
        .dev-table tbody td { border-color: #21262d; padding: .65rem 1rem; font-size: 13px; }
        .dev-table tbody tr:hover { background: #1c2128; }
        .dev-input    { background: #0d1117; border: 1px solid var(--dev-border); color: var(--dev-text); border-radius: 6px; }
        .dev-input:focus { background: #0d1117; border-color: var(--dev-primary); color: var(--dev-text); box-shadow: 0 0 0 3px rgba(102,126,234,.2); }
        .dev-input::placeholder { color: var(--dev-muted); }
        .dev-select   { background: #0d1117; border: 1px solid var(--dev-border); color: var(--dev-text); }
        .dev-select:focus { background: #0d1117; border-color: var(--dev-primary); color: var(--dev-text); box-shadow: 0 0 0 3px rgba(102,126,234,.2); }
        .dev-btn-primary { background: var(--dev-primary); border: none; color: #fff; }
        .dev-btn-primary:hover { background: #5a6fd6; color: #fff; }
        a { color: var(--dev-primary); }
        a:hover { color: #8fa5f0; }
        .form-label { color: var(--dev-muted); font-size: 12px; }
        .invalid-feedback { font-size: 12px; }
        .alert-success { background: rgba(63,185,80,.1); border-color: rgba(63,185,80,.3); color: var(--dev-success); }
        .alert-danger  { background: rgba(248,81,73,.1); border-color: rgba(248,81,73,.3); color: var(--dev-danger); }
        .alert-warning { background: rgba(210,153,34,.1); border-color: rgba(210,153,34,.3); color: #e3b341; }
    </style>

    @stack('styles')
</head>
<body>

    {{-- Navbar ----------------------------------------------------------------}}
    <nav class="dev-navbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('developer.modulos.index') }}" class="text-decoration-none fw-bold" style="color: var(--dev-primary);">
                <i class="ti ti-code me-1"></i>Dev Panel
            </a>
            <span class="dev-badge">{{ config('app.name') }}</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('developer.modulos.index') }}" class="btn btn-sm btn-outline-secondary" style="border-color: var(--dev-border); color: var(--dev-text);">
                <i class="ti ti-layout-grid me-1"></i>Módulos
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary" style="border-color: var(--dev-border); color: var(--dev-text);">
                <i class="ti ti-arrow-left me-1"></i>App
            </a>
            <form method="POST" action="{{ route('developer.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="ti ti-logout me-1"></i>Salir
                </button>
            </form>
        </div>
    </nav>

    {{-- Content ---------------------------------------------------------------}}
    <div class="container-fluid px-4 py-4" style="max-width: 1100px;">

        {{-- Alertas globales --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('assets/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
