<?php

namespace App\Http\Controllers\PanelEspecialista;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Support\Facades\Auth;

class PacienteController extends Controller
{
    public function show(int $pacienteId)
    {
        $especialista = Auth::guard('especialista')->user();

        // El especialista solo puede ver pacientes con los que ha tenido citas
        $paciente = Paciente::with([
            'persona',
            'casos' => fn($q) => $q
                ->where('especialista_id', $especialista->id)
                ->with(['consultas' => fn($c) => $c->with('adjuntos')->orderBy('fecha_hora', 'desc')])
                ->orderBy('fecha_apertura', 'desc'),
            'citas' => fn($q) => $q->where('especialista_id', $especialista->id),
        ])->findOrFail($pacienteId);

        // Verificar que este especialista realmente ha atendido a este paciente
        abort_unless(
            $paciente->citas->isNotEmpty() || $paciente->casos->isNotEmpty(),
            403,
            'No tiene acceso a este expediente.'
        );

        return view('panel_especialista.paciente.show', compact('paciente', 'especialista'));
    }
}
