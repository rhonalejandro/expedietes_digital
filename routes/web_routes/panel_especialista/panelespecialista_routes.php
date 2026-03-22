<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PanelEspecialista\PanelAuthController;
use App\Http\Controllers\PanelEspecialista\AgendaController;
use App\Http\Controllers\PanelEspecialista\AtencionController;
use App\Http\Controllers\PanelEspecialista\PacienteController;
use App\Http\Controllers\PanelEspecialista\PdfController;

/*
|--------------------------------------------------------------------------
| Panel Especialista — Guard: 'especialista'
|--------------------------------------------------------------------------
*/

// ── Autenticación del especialista (sin guard, son las rutas públicas del panel) ──
Route::prefix('panel')->name('panel.')->group(function () {

    Route::get('/login',  [PanelAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [PanelAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout',[PanelAuthController::class, 'logout'])->name('logout');

});

// ── Rutas protegidas del panel ────────────────────────────────────────────────
Route::middleware(['auth:especialista'])
    ->prefix('panel')
    ->name('panel.')
    ->group(function () {

        // Agenda del día
        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');

        // Atención del paciente
        Route::get('/atencion/{citaId}',  [AtencionController::class, 'show'])->name('atencion.show');
        Route::post('/atencion/{citaId}', [AtencionController::class, 'guardar'])->name('atencion.guardar');
        Route::delete('/atencion/foto/{id}', [AtencionController::class, 'eliminarFoto'])->name('atencion.foto.eliminar');

        // Expediente del paciente (solo datos personales + casos propios)
        Route::get('/paciente/{pacienteId}', [PacienteController::class, 'show'])->name('paciente.show');

        // PDFs de consulta
        Route::get('/paciente/{pacienteId}/consulta/{consultaId}/pdf',    [PdfController::class, 'consultaPdf'])->name('paciente.consulta.pdf');
        Route::get('/paciente/{pacienteId}/consulta/{consultaId}/receta', [PdfController::class, 'recetaPdf'])->name('paciente.consulta.receta');

    });
