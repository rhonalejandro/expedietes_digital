<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspecialistaController;
use App\Http\Controllers\LogEspecialistaController;

Route::middleware(['auth'])->group(function () {

    Route::resource('especialistas', EspecialistaController::class);

    Route::post('especialistas/{id}/toggle', [EspecialistaController::class, 'toggleEstado'])
         ->name('especialistas.toggle');

    Route::get('especialistas/{id}/actividades', [LogEspecialistaController::class, 'porEspecialista'])
         ->name('especialistas.actividades');
});
