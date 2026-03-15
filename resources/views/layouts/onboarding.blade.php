<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Onboarding - Expediente Digital')</title>
    <!-- Bootstrap 3 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #eaf6fb; /* azul médico claro */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .onboarding-panel {
            background: #fff;
            border: 1px solid #b2d8e6;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin: 0 auto;
            max-width: 500px;
        }
        .onboarding-header {
            background: #007b8a; /* azul médico */
            color: #fff;
            border-radius: 6px 6px 0 0;
            padding: 18px 24px;
        }
        .onboarding-title {
            margin: 0;
            font-size: 1.7em;
            font-weight: 600;
        }
        .onboarding-form label {
            color: #007b8a;
        }
        .onboarding-form .btn-primary {
            background: #007b8a;
            border-color: #007b8a;
        }
        .onboarding-form .btn-primary:hover {
            background: #005f6b;
            border-color: #005f6b;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-6 col-lg-5" style="float:none; margin:0 auto;">
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
