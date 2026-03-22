<?php

namespace App\Http\Controllers\PanelEspecialista;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $especialista = Auth::guard('especialista')->user();
        $hoy          = now()->toDateString();

        // Fecha seleccionada (parámetro ?fecha=YYYY-MM-DD, por defecto hoy)
        $fecha = $request->filled('fecha') && strtotime($request->fecha)
            ? $request->fecha
            : $hoy;

        $fechaCarbon  = Carbon::parse($fecha)->locale('es');
        $fechaAnterior = $fechaCarbon->copy()->subDay()->toDateString();
        $fechaSiguiente = $fechaCarbon->copy()->addDay()->toDateString();
        $esHoy        = $fecha === $hoy;

        $citas = Cita::with(['paciente.persona', 'servicio', 'sucursal'])
            ->where('especialista_id', $especialista->id)
            ->where('fecha', $fecha)
            ->whereNotIn('estatus', ['cancelada'])
            ->orderBy('hora_inicio')
            ->get();

        return view('panel_especialista.agenda.index', compact(
            'especialista', 'citas', 'hoy', 'fecha',
            'fechaCarbon', 'fechaAnterior', 'fechaSiguiente', 'esHoy'
        ));
    }
}
