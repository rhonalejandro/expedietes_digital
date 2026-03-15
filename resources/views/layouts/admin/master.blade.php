<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Administrativo') - Expediente Digital</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    
    <!-- Animation css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/animation/animate.min.css') }}?v={{ time() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" crossorigin href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <!-- Tabler icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}?v={{ time() }}">

    <!-- Flag Icon css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/flag-icons-master/flag-icon.css') }}?v={{ time() }}">

    <!-- Prism css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/prism/prism.min.css') }}?v={{ time() }}">

    <!-- Bootstrap css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}?v={{ time() }}">

    <!-- Simplebar css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/simplebar/simplebar.css') }}?v={{ time() }}">

    <!-- Template CSS (ya compilado del build) -->
    <link rel="stylesheet" href="{{ asset('build/assets/style-Cuxwy5N_.css') }}?v={{ time() }}">

    <!-- Custom Font Size (reducir 2px globalmente) -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom-font-size.css') }}?v={{ time() }}">

    <!-- Sidebar customizations -->
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}?v={{ time() }}">

    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Loader -->
        <div class="loader-wrapper">
            <div class="loader_24"></div>
        </div>
        
        <!-- Sidebar -->
        @include('layouts.admin.sidebar')
        
        <div class="app-content">
            <!-- Header -->
            @include('layouts.admin.header')
            
            <!-- Main Content -->
            <main>
                @yield('content')
            </main>
            
            <!-- Tap on top -->
            <div class="go-top">
                <span class="progress-value">
                    <i class="ti ti-arrow-up"></i>
                </span>
            </div>
            
            <!-- Footer -->
            @include('layouts.admin.footer')
        </div>
    </div>
    
    <!-- Customizer -->
    <div id="customizer"></div>
    
    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.6.3.min.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/vendor/simplebar/simplebar.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/vendor/phosphor/phosphor.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/customizer.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/vendor/prism/prism.min.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/script.js') }}?v={{ time() }}"></script>

    @stack('scripts')

    <script>
    function toggleFullscreen() {
        const icon = document.getElementById('fs-icon');
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
