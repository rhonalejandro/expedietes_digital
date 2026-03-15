<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\DeveloperAuthController;
use App\Http\Controllers\Developer\DeveloperModulosController;

/*
|--------------------------------------------------------------------------
| Developer Panel Routes
|--------------------------------------------------------------------------
|
| Área exclusiva para el desarrollador. Acceso protegido por TOTP.
| Gestiona módulos del sistema y sus acciones (permisos granulares).
|
| Setup inicial: php artisan developer:setup-totp
|
*/

Route::prefix('developer')->name('developer.')->group(function () {

    // ── Auth TOTP ─────────────────────────────────────────────────────────────
    Route::get('login',  [DeveloperAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [DeveloperAuthController::class, 'login']);
    Route::post('logout', [DeveloperAuthController::class, 'logout'])->name('logout');

    // ── Rutas protegidas con middleware developer ──────────────────────────────
    Route::middleware('developer')->group(function () {

        // Módulos — CRUD
        Route::get('/',                      [DeveloperModulosController::class, 'index'])->name('modulos.index');
        Route::get('modulos',                [DeveloperModulosController::class, 'index']);
        Route::get('modulos/create',         [DeveloperModulosController::class, 'create'])->name('modulos.create');
        Route::post('modulos',               [DeveloperModulosController::class, 'store'])->name('modulos.store');
        Route::get('modulos/{id}',           [DeveloperModulosController::class, 'show'])->name('modulos.show');
        Route::get('modulos/{id}/edit',      [DeveloperModulosController::class, 'edit'])->name('modulos.edit');
        Route::put('modulos/{id}',           [DeveloperModulosController::class, 'update'])->name('modulos.update');
        Route::delete('modulos/{id}',        [DeveloperModulosController::class, 'destroy'])->name('modulos.destroy');

        // Acciones (permisos) dentro de un módulo
        Route::get('modulos/{moduloId}/acciones/create',         [DeveloperModulosController::class, 'createAccion'])->name('acciones.create');
        Route::post('modulos/{moduloId}/acciones',               [DeveloperModulosController::class, 'storeAccion'])->name('acciones.store');
        Route::get('modulos/{moduloId}/acciones/{permisoId}/edit', [DeveloperModulosController::class, 'editAccion'])->name('acciones.edit');
        Route::put('modulos/{moduloId}/acciones/{permisoId}',    [DeveloperModulosController::class, 'updateAccion'])->name('acciones.update');
        Route::delete('modulos/{moduloId}/acciones/{permisoId}', [DeveloperModulosController::class, 'destroyAccion'])->name('acciones.destroy');

    });
});
