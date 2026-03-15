<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogClienteController;
use App\Http\Controllers\PacienteController;

/*
|--------------------------------------------------------------------------
| Rutas de Pacientes
|--------------------------------------------------------------------------
|
| CRUD completo + toggle de estado vía AJAX.
| Soft delete en destroy (no elimina físicamente).
|
| Índice de consultas registrado en: docs/indice_consultas.md
|
*/

Route::middleware(['auth'])->group(function () {

    // Búsqueda rápida autocomplete (debe ir ANTES del resource)
    Route::get('pacientes/buscar', [PacienteController::class, 'buscar'])
         ->name('pacientes.buscar');

    // Resource: index, create, store, show, edit, update, destroy
    Route::resource('pacientes', PacienteController::class);

    // Toggle activo/inactivo vía AJAX (POST para respetar CSRF)
    Route::post('pacientes/{id}/toggle', [PacienteController::class, 'toggleEstado'])
         ->name('pacientes.toggle');

    // Actividades recientes del paciente (AJAX/JSON, paginación infinita)
    Route::get('pacientes/{id}/actividades', [LogClienteController::class, 'porPaciente'])
         ->name('pacientes.actividades');
});
