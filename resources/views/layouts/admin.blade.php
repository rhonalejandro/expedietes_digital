<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrativo')</title>
    <!-- Bootstrap 3 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/admin-custom.css">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-inverse navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Expediente Digital</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Empresas</a></li>
                <li><a href="#">Pacientes</a></li>
                <li><a href="#">Doctores</a></li>
                <li><a href="#">Cerrar sesión</a></li>
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="#">Dashboard</a></li>
                    <li><a href="#">Empresas</a></li>
                    <li><a href="#">Sucursales</a></li>
                    <li><a href="#">Pacientes</a></li>
                    <li><a href="#">Doctores</a></li>
                    <li><a href="#">Citas</a></li>
                    <li><a href="#">Casos</a></li>
                    <li><a href="#">Consultas</a></li>
                    <li><a href="#">Servicios</a></li>
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h1 class="page-header">@yield('page-title', 'Panel Administrativo')</h1>
                @yield('content')
            </div>
        </div>
    </div>
    <footer class="footer text-center">
        <div class="container">
            <p class="text-muted">&copy; 2026 Expediente Digital</p>
        </div>
    </footer>
    <!-- Bootstrap 3 JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    @stack('scripts')
</body>
</html>
