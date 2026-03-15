<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicioController;

Route::middleware(['auth'])->prefix('servicios')->name('servicios.')->group(function () {
    Route::get('/',              [ServicioController::class, 'index'])->name('index');
    Route::post('/',             [ServicioController::class, 'store'])->name('store');
    Route::get('/{id}',          [ServicioController::class, 'show'])->name('show');
    Route::put('/{id}',          [ServicioController::class, 'update'])->name('update');
    Route::delete('/{id}',       [ServicioController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/toggle', [ServicioController::class, 'toggleEstado'])->name('toggle');
});
