<?php

namespace App\Http\Controllers;

use App\Models\LogCliente;
use App\Models\Paciente;
use Illuminate\Http\JsonResponse;

/**
 * LogClienteController
 * Expone el historial de actividades de un paciente para el componente
 * "actividades-recientes" (paginación infinita vía AJAX/JSON).
 */
class LogClienteController extends Controller
{
    /**
     * Devuelve los logs paginados de un paciente.
     * GET /pacientes/{id}/actividades?page=N
     */
    public function porPaciente(int $id): JsonResponse
    {
        Paciente::findOrFail($id);

        $logs = LogCliente::where('cliente_id', $id)
            ->with('usuario:id,nombre')
            ->orderByDesc('fecha')
            ->paginate(10);

        return response()->json($logs);
    }
}
