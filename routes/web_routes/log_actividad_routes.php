<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogActividadController;

/*
|--------------------------------------------------------------------------
| Log Actividad — Endpoint unificado
|--------------------------------------------------------------------------
| Alimenta el componente <x-log-actividad modulo="..." :registro-id="...">
| GET /log-actividad/{modulo}/{id}?page=N
| Módulos: pacientes | especialistas | citas | empresa
*/

Route::middleware(['auth'])->group(function () {
    Route::get('log-actividad/{modulo}/{id}', [LogActividadController::class, 'index'])
         ->name('log.actividad');
});
