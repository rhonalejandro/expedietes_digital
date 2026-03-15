
<?php

use Illuminate\Support\Facades\Route;


// Agrupar rutas bajo el middleware web
Route::middleware(['web'])->group(function () {
    require base_path('routes/web_routes/auth/auth_routes.php');
    
    // Rutas de configuración
    require base_path('routes/web_routes/settings/settings.php');

    // Rutas de pacientes
    require base_path('routes/web_routes/pacientes/pacientes_routes.php');

    // Rutas de especialistas
    require base_path('routes/web_routes/especialistas/especialistas_routes.php');

    // Rutas de citas
    require base_path('routes/web_routes/citas/citas_routes.php');

    // Rutas de servicios
    require base_path('routes/web_routes/servicios/servicios_routes.php');

    // Developer Panel (TOTP-protected, fuera del middleware auth)
    require base_path('routes/web_routes/developer/developer_routes.php');

    Route::get('/', function () {
        return redirect()->route('login');
    });
});
