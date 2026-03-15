<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\EmpresaController;
use App\Http\Controllers\Settings\SucursalesController;
use App\Http\Controllers\Settings\PermissionsController;
use App\Http\Controllers\Settings\PermissionTemplatesController;
use App\Http\Controllers\Settings\UserPermissionsController;

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
|
| Rutas para el módulo de configuración de empresa y sucursales.
|
*/

Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {

    // Vista principal
    Route::get('/', [SettingsController::class, 'index'])->name('index');

    // Empresa
    Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');

    // Sucursales
    Route::get('/sucursales', [SucursalesController::class, 'index'])->name('sucursales.index');
    Route::post('/sucursal', [SucursalesController::class, 'store'])->name('sucursal.store');
    Route::put('/sucursal/{id}', [SucursalesController::class, 'update'])->name('sucursal.update');
    Route::delete('/sucursal/{id}', [SucursalesController::class, 'destroy'])->name('sucursal.destroy');
    Route::get('/sucursal/{id}/toggle', [SucursalesController::class, 'toggleStatus'])->name('sucursal.toggle');

    // Permisos
    Route::get('/permisos', [PermissionsController::class, 'index'])->name('permissions.index');
    Route::get('/permisos/create', [PermissionsController::class, 'create'])->name('permissions.create');
    Route::post('/permisos', [PermissionsController::class, 'store'])->name('permissions.store');
    Route::get('/permisos/{id}/edit', [PermissionsController::class, 'edit'])->name('permissions.edit');
    Route::put('/permisos/{id}', [PermissionsController::class, 'update'])->name('permissions.update');
    Route::post('/permisos/{id}/toggle', [PermissionsController::class, 'toggleStatus'])->name('permissions.toggle');
    Route::delete('/permisos/{id}', [PermissionsController::class, 'destroy'])->name('permissions.destroy');

    // Plantillas de Permisos
    Route::get('/permisos/plantillas', [PermissionTemplatesController::class, 'index'])->name('permissions.templates.index');
    Route::get('/permisos/plantillas/create', [PermissionTemplatesController::class, 'create'])->name('permissions.templates.create');
    Route::post('/permisos/plantillas', [PermissionTemplatesController::class, 'store'])->name('permissions.templates.store');
    Route::get('/permisos/plantillas/{id}/edit', [PermissionTemplatesController::class, 'edit'])->name('permissions.templates.edit');
    Route::put('/permisos/plantillas/{id}', [PermissionTemplatesController::class, 'update'])->name('permissions.templates.update');
    Route::post('/permisos/plantillas/{id}/toggle', [PermissionTemplatesController::class, 'toggleStatus'])->name('permissions.templates.toggle');
    Route::delete('/permisos/plantillas/{id}', [PermissionTemplatesController::class, 'destroy'])->name('permissions.templates.destroy');

    // Asignación de Permisos a Usuarios
    Route::get('/permisos/usuarios', [UserPermissionsController::class, 'index'])->name('permissions.users.index');
    Route::get('/permisos/usuarios/{id}', [UserPermissionsController::class, 'show'])->name('permissions.users.show');
    Route::get('/permisos/usuarios/{id}/edit', [UserPermissionsController::class, 'edit'])->name('permissions.users.edit');
    Route::put('/permisos/usuarios/{id}', [UserPermissionsController::class, 'update'])->name('permissions.users.update');
    Route::post('/permisos/usuarios/asignar-plantilla', [UserPermissionsController::class, 'assignTemplate'])->name('permissions.users.assign-template');

});
