<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - Expediente Digital</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    
    <!-- Animation css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/animation/animate.min.css') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" crossorigin href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    
    <!-- Tabler icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}">
    
    <!-- Bootstrap css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    
    <!-- Simplebar css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/simplebar/simplebar.css') }}">

    <!-- Template CSS (ya compilado del build) -->
    <link rel="stylesheet" href="{{ asset('build/assets/style-Cuxwy5N_.css') }}">

    <!-- Custom Font Size (reducir 2px globalmente) -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom-font-size.css') }}">

    @stack('styles')
</head>
<body>
    <div class="app-wrapper d-block">
        @yield('content')
    </div>
    
    <!-- Bootstrap js -->
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
