<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LogSistemaHelper;
use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Especialista;
use App\Models\HorarioEspecialista;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiCitaController extends Controller
{
    private const ESTATUS  = ['pendiente', 'confirmada', 'atendida', 'cancelada', 'no_asistio'];
    private const ORIGENES = ['web', 'chatwoot', 'mobile', 'telefono'];

    // ── Listar citas de un paciente ───────────────────────────────────────────
    // GET /api/v1/citas?paciente_id=X&lead_tel=+507...&historico=1

    public function index(Request $request): JsonResponse
    {
        $query = Cita::with(['especialista.persona', 'servicio', 'sucursal']);

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        } elseif ($request->filled('lead_tel')) {
            $query->where('telefono_lead', 'like', '%' . preg_replace('/\D/', '', $request->lead_tel) . '%');
        } else {
            return response()->json(['error' => 'Se requiere paciente_id o lead_tel.'], 422);
        }

        if (!$request->boolean('historico')) {
            $query->where('fecha', '>=', now()->toDateString())
                  ->whereNotIn('estatus', ['cancelada']);
        }

        $citas = $query->orderBy('fecha')->orderBy('hora_inicio')->limit(20)->get();

        return response()->json($citas->map(fn($c) => $this->formatCita($c)));
    }

    // ── Crear cita ────────────────────────────────────────────────────────────
    // POST /api/v1/citas

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'especialista_id' => 'required|exists:especialistas,id',
            'sucursal_id'     => 'required|exists:sucursales,id',
            'paciente_id'     => 'nullable|exists:pacientes,id',
            'servicio_id'     => 'nullable|exists:servicios,id',
            'nombre_lead'     => 'nullable|string|max:150',
            'telefono_lead'   => 'nullable|string|max:50',
            'fecha'           => 'required|date|after_or_equal:today',
            'hora_inicio'     => 'required|date_format:H:i',
            'hora_fin'        => 'required|date_format:H:i|after:hora_inicio',
            'motivo'          => 'nullable|string|max:500',
            'origen'          => 'nullable|in:' . implode(',', self::ORIGENES),
        ]);

        if (empty($data['paciente_id']) && empty($data['nombre_lead'])) {
            return response()->json(['error' => 'Se requiere paciente_id o nombre_lead.'], 422);
        }

        $this->checkSolapamiento($data);

        $data['estatus'] = 'pendiente';
        $data['origen']  = $data['origen'] ?? 'chatwoot';

        $cita = Cita::create($data);
        LogSistemaHelper::logCitas('creada', $cita->id, actual: $data);

        return response()->json([
            'success' => true,
            'cita'    => $this->formatCita($cita->load(['especialista.persona', 'servicio', 'sucursal'])),
        ], 201);
    }

    // ── Cambiar estatus ───────────────────────────────────────────────────────
    // PATCH /api/v1/citas/{id}/estatus

    public function cambiarEstatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'estatus' => 'required|in:' . implode(',', self::ESTATUS),
        ]);

        $cita     = Cita::findOrFail($id);
        $anterior = $cita->estatus;

        $cita->update(['estatus' => $request->estatus]);

        LogSistemaHelper::logCitas('estatus_cambiado', $cita->id,
            ['estatus' => $anterior], ['estatus' => $request->estatus]);

        return response()->json([
            'success' => true,
            'estatus' => $cita->estatus,
            'cita'    => $this->formatCita($cita->load(['especialista.persona', 'servicio', 'sucursal'])),
        ]);
    }

    // ── Disponibilidad mensual ────────────────────────────────────────────────
    // GET /api/v1/especialistas/{id}/disponibilidad?mes=YYYY-MM

    public function disponibilidad(int $especialistaId): JsonResponse
    {
        $mes    = request('mes', now()->format('Y-m'));
        $inicio = Carbon::parse($mes . '-01')->startOfMonth();
        $fin    = $inicio->copy()->endOfMonth();

        $diasMap = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 0 => 'domingo',
        ];

        $horarios = HorarioEspecialista::where('especialista_id', $especialistaId)
            ->get()->keyBy('dia_semana');

        $citasPorDia = Cita::where('especialista_id', $especialistaId)
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->whereNotIn('estatus', ['cancelada'])
            ->selectRaw('fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $dias = [];
        for ($d = $inicio->copy(); $d->lte($fin); $d->addDay()) {
            $diaNombre = $diasMap[$d->dayOfWeek];
            $fechaStr  = $d->toDateString();

            if (!isset($horarios[$diaNombre])) {
                $dias[$fechaStr] = 'sin_horario';
                continue;
            }

            $horario    = $horarios[$diaNombre];
            $slotsTotal = (int) floor(
                (strtotime($horario->hora_fin) - strtotime($horario->hora_inicio))
                / ($horario->duracion_cita * 60)
            );

            $dias[$fechaStr] = ((int) ($citasPorDia[$fechaStr] ?? 0)) >= $slotsTotal ? 'lleno' : 'disponible';
        }

        return response()->json($dias);
    }

    // ── Slots de horas disponibles ────────────────────────────────────────────
    // GET /api/v1/especialistas/{id}/horas-disponibles?fecha=YYYY-MM-DD

    public function horasDisponibles(int $especialistaId): JsonResponse
    {
        $fecha = request('fecha');
        if (!$fecha) {
            return response()->json(['error' => 'Falta el parámetro fecha.'], 422);
        }

        $diasMap = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 0 => 'domingo',
        ];

        $dow     = Carbon::parse($fecha)->dayOfWeek;
        $horario = HorarioEspecialista::where('especialista_id', $especialistaId)
            ->where('dia_semana', $diasMap[$dow])
            ->first();

        if (!$horario) {
            return response()->json([]);
        }

        $duracion = $horario->duracion_cita;
        $cursor   = Carbon::parse($fecha . ' ' . $horario->hora_inicio);
        $limFin   = Carbon::parse($fecha . ' ' . $horario->hora_fin);
        $slots    = [];

        while ($cursor->copy()->addMinutes($duracion)->lte($limFin)) {
            $slots[] = [
                'hora_inicio' => $cursor->format('H:i'),
                'hora_fin'    => $cursor->copy()->addMinutes($duracion)->format('H:i'),
            ];
            $cursor->addMinutes($duracion);
        }

        $citasDelDia = Cita::where('especialista_id', $especialistaId)
            ->where('fecha', $fecha)
            ->whereNotIn('estatus', ['cancelada'])
            ->get(['hora_inicio', 'hora_fin']);

        return response()->json(array_map(function ($slot) use ($citasDelDia) {
            $hiSlot  = strtotime($slot['hora_inicio']);
            $hfSlot  = strtotime($slot['hora_fin']);
            $ocupado = $citasDelDia->contains(fn($c) =>
                strtotime($c->hora_inicio) < $hfSlot && strtotime($c->hora_fin) > $hiSlot
            );
            return array_merge($slot, ['disponible' => !$ocupado]);
        }, $slots));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function formatCita(Cita $c): array
    {
        return [
            'id'          => $c->id,
            'fecha'       => $c->fecha?->format('Y-m-d'),
            'hora_inicio' => substr($c->hora_inicio ?? '', 0, 5),
            'hora_fin'    => substr($c->hora_fin    ?? '', 0, 5),
            'especialista'=> $c->especialista?->nombre_completo,
            'servicio'    => $c->servicio?->nombre,
            'sucursal'    => $c->sucursal?->nombre,
            'estatus'     => $c->estatus,
            'motivo'      => $c->motivo,
            'origen'      => $c->origen,
            'paciente_id' => $c->paciente_id,
            'nombre_lead' => $c->nombre_lead,
        ];
    }

    private function checkSolapamiento(array $data, ?int $citaId = null): void
    {
        $conflicto = Cita::where('especialista_id', $data['especialista_id'])
            ->where('fecha', $data['fecha'])
            ->whereNotIn('estatus', ['cancelada'])
            ->where('hora_inicio', '<', $data['hora_fin'])
            ->where('hora_fin',    '>', $data['hora_inicio'])
            ->when($citaId, fn($q) => $q->where('id', '!=', $citaId))
            ->first();

        if ($conflicto) {
            $hi = substr($conflicto->hora_inicio, 0, 5);
            $hf = substr($conflicto->hora_fin,    0, 5);
            throw ValidationException::withMessages([
                'hora_inicio' => ["El especialista ya tiene una cita de {$hi} a {$hf}."],
            ]);
        }
    }
}
