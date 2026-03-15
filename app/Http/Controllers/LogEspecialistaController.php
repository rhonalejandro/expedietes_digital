<?php

namespace App\Http\Controllers;

use App\Models\Especialista;
use App\Models\LogEspecialista;
use Illuminate\Http\JsonResponse;

class LogEspecialistaController extends Controller
{
    /**
     * GET /especialistas/{id}/actividades?page=N
     */
    public function porEspecialista(int $id): JsonResponse
    {
        Especialista::findOrFail($id);

        $logs = LogEspecialista::where('especialista_id', $id)
            ->with('usuario:id,nombre')
            ->orderByDesc('fecha')
            ->paginate(10);

        return response()->json($logs);
    }
}
