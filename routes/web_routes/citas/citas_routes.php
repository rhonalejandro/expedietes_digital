<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;

Route::middleware(['auth'])->prefix('citas')->name('citas.')->group(function () {

    // Calendario (vista principal)
    Route::get('/',              [CitaController::class, 'index'])->name('index');

    // API JSON para FullCalendar
    Route::get('/eventos',       [CitaController::class, 'eventos'])->name('eventos');

    // Disponibilidad del especialista (antes de /{id} para evitar conflictos)
    Route::get('/disponibilidad/{id}',    [CitaController::class, 'disponibilidad'])->name('disponibilidad');
    Route::get('/horas-disponibles/{id}', [CitaController::class, 'horasDisponibles'])->name('horas_disponibles');

    // CRUD vía AJAX (modal)
    Route::post('/',             [CitaController::class, 'store'])->name('store');
    Route::get('/{id}',          [CitaController::class, 'show'])->name('show');
    Route::put('/{id}',          [CitaController::class, 'update'])->name('update');
    Route::delete('/{id}',       [CitaController::class, 'destroy'])->name('destroy');

    // Cambio rápido de estatus
    Route::patch('/{id}/estatus', [CitaController::class, 'cambiarEstatus'])->name('estatus');
});
