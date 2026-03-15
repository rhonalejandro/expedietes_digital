<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Caso;
use App\Models\Especialista;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy          = Carbon::today();
        $mesInicio    = $hoy->copy()->startOfMonth();
        $mesFin       = $hoy->copy()->endOfMonth();
        $semanaInicio = $hoy->copy()->startOfWeek();
        $semanaFin    = $hoy->copy()->endOfWeek();

        // ── KPIs ─────────────────────────────────────────────────────────────
        $totalPacientes   = Paciente::where('estado', true)->count();
        $totalEspecialistas = Especialista::where('estado', true)->count();

        $citasHoy         = Cita::whereDate('fecha', $hoy)->count();
        $citasHoyConfirmadas = Cita::whereDate('fecha', $hoy)->where('estatus', 'confirmada')->count();
        $citasHoyPendientes  = Cita::whereDate('fecha', $hoy)->where('estatus', 'pendiente')->count();

        $casosActivos     = Caso::where('estado', 'activo')->count();
        $citasMes         = Cita::whereBetween('fecha', [$mesInicio, $mesFin])->count();
        $nuevosPacientesMes = Paciente::where('created_at', '>=', $mesInicio)->count();

        // ── Citas por día de la semana actual (chart) ─────────────────────
        $citasSemana = [];
        for ($i = 0; $i < 7; $i++) {
            $dia = $semanaInicio->copy()->addDays($i);
            $citasSemana[] = Cita::whereDate('fecha', $dia)
                ->whereNotIn('estatus', ['cancelada'])
                ->count();
        }

        // ── Distribución por estatus este mes ─────────────────────────────
        $distribucionEstatus = Cita::whereBetween('fecha', [$mesInicio, $mesFin])
            ->selectRaw('estatus, COUNT(*) as total')
            ->groupBy('estatus')
            ->pluck('total', 'estatus');

        // ── Top 6 especialistas con más citas esta semana ─────────────────
        $topEspecialistas = Cita::whereBetween('fecha', [$semanaInicio, $semanaFin])
            ->whereNotIn('estatus', ['cancelada'])
            ->selectRaw('especialista_id, COUNT(*) as total')
            ->groupBy('especialista_id')
            ->orderByDesc('total')
            ->limit(6)
            ->with('especialista.persona')
            ->get();

        // ── Próximas 6 citas (hoy en adelante) ───────────────────────────
        $proximasCitas = Cita::with(['especialista.persona', 'paciente.persona', 'sucursal'])
            ->whereDate('fecha', '>=', $hoy)
            ->whereIn('estatus', ['confirmada', 'pendiente'])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'totalPacientes', 'totalEspecialistas',
            'citasHoy', 'citasHoyConfirmadas', 'citasHoyPendientes',
            'casosActivos', 'citasMes', 'nuevosPacientesMes',
            'citasSemana', 'distribucionEstatus',
            'topEspecialistas', 'proximasCitas'
        ));
    }
}
