<?php

use App\Http\Controllers\Api\ApiCatalogoController;
use App\Http\Controllers\Api\ApiCitaController;
use App\Http\Controllers\Api\ApiLeadController;
use App\Http\Controllers\Api\ApiPacienteController;
use App\Http\Controllers\Api\ChatwootWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Integración Chatwoot ↔ CRM Global Feet Panama
|--------------------------------------------------------------------------
| Autenticación: Bearer token (CHATWOOT_WIDGET_TOKEN en .env)
| Todos los endpoints retornan JSON.
|--------------------------------------------------------------------------
*/

// ── Webhook de Chatwoot (sin token, verificado por HMAC) ──────────────────────
Route::post('/v1/webhook/chatwoot', [ChatwootWebhookController::class, 'handle']);

// ── Endpoints protegidos por API token ───────────────────────────────────────
Route::middleware('api.token')->prefix('v1')->group(function () {

    // Pacientes
    Route::get('/pacientes/buscar-por-telefono', [ApiPacienteController::class, 'buscarPorTelefono']);
    Route::get('/pacientes/buscar',              [ApiPacienteController::class, 'buscar']);
    Route::get('/pacientes/{id}',                [ApiPacienteController::class, 'show']);
    Route::post('/pacientes',                    [ApiPacienteController::class, 'store']);

    // Citas
    Route::get('/citas',                              [ApiCitaController::class, 'index']);
    Route::post('/citas',                             [ApiCitaController::class, 'store']);
    Route::patch('/citas/{id}/estatus',               [ApiCitaController::class, 'cambiarEstatus']);

    // Disponibilidad (reutiliza métodos de ApiCitaController)
    Route::get('/especialistas/{id}/disponibilidad',  [ApiCitaController::class, 'disponibilidad']);
    Route::get('/especialistas/{id}/horas-disponibles', [ApiCitaController::class, 'horasDisponibles']);

    // Leads
    Route::get('/leads',                  [ApiLeadController::class, 'index']);
    Route::post('/leads',                 [ApiLeadController::class, 'store']);
    Route::put('/leads/{id}/convertir',   [ApiLeadController::class, 'convertir']);
    Route::patch('/leads/{id}/estatus',   [ApiLeadController::class, 'cambiarEstatus']);

    // Catálogos
    Route::get('/especialistas', [ApiCatalogoController::class, 'especialistas']);
    Route::get('/servicios',     [ApiCatalogoController::class, 'servicios']);
    Route::get('/sucursales',    [ApiCatalogoController::class, 'sucursales']);
});
