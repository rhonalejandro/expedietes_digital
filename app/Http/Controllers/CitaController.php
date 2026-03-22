<?php

namespace App\Http\Controllers;

use App\Helpers\LogSistemaHelper;
use App\Models\Cita;
use App\Models\Especialista;
use App\Models\HorarioEspecialista;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CitaController extends Controller
{
    // Estatus válidos
    private const ESTATUS = ['pendiente', 'confirmada', 'atendida', 'cancelada', 'no_asistio'];
    private const ORIGENES = ['web', 'chatwoot', 'mobile', 'telefono'];

    // ── Calendario principal ──────────────────────────────────────────────────

    public function index()
    {
        $especialistas = Especialista::with('persona')->activos()->get();
        $sucursales    = Sucursal::where('estado', true)->get();
        $servicios     = Servicio::where('estado', true)->get();

        $sucursalHorario = $sucursales->keyBy('id')->map(fn($s) => [
            'apertura' => substr($s->hora_apertura ?? '09:00:00', 0, 5),
            'cierre'   => substr($s->hora_cierre   ?? '18:00:00', 0, 5),
        ]);

        $modoAgenda = \App\Models\Empresa::first()?->modo_agenda ?? 'estricto';

        return view('modules.citas.index', compact('especialistas', 'sucursales', 'servicios', 'sucursalHorario', 'modoAgenda'));
    }

    // ── API JSON para FullCalendar ─────────────────────────────────────────────

    public function eventos(Request $request): JsonResponse
    {
        $query = Cita::with(['especialista.persona', 'paciente.persona', 'servicio'])
            ->whereBetween('fecha', [
                $request->get('start', now()->startOfMonth()->toDateString()),
                $request->get('end',   now()->endOfMonth()->toDateString()),
            ]);

        // Filtro por especialista(s)
        if ($request->filled('especialistas')) {
            $ids = array_filter(explode(',', $request->especialistas));
            if (!empty($ids)) {
                $query->whereIn('especialista_id', $ids);
            }
        }

        // Filtro por sucursal
        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        $citas = $query->get();

        // Colores por estatus
        $colores = [
            'pendiente'   => ['bg' => '#94a3b8', 'border' => '#64748b', 'text' => '#ffffff'],
            'confirmada'  => ['bg' => '#38a169', 'border' => '#2f8a59', 'text' => '#ffffff'],
            'atendida'    => ['bg' => '#4a5568', 'border' => '#2d3748', 'text' => '#ffffff'],
            'cancelada'   => ['bg' => '#e53e3e', 'border' => '#c53030', 'text' => '#ffffff'],
            'no_asistio'  => ['bg' => '#dd6b20', 'border' => '#c05621', 'text' => '#ffffff'],
        ];

        $eventos = $citas->map(function (Cita $cita) use ($colores) {
            $color = $colores[$cita->estatus] ?? $colores['pendiente'];
            return [
                'id'              => $cita->id,
                'title'           => $cita->nombre_paciente,
                'start'           => $cita->fecha->format('Y-m-d') . 'T' . $cita->hora_inicio,
                'end'             => $cita->fecha->format('Y-m-d') . 'T' . $cita->hora_fin,
                'backgroundColor' => $color['bg'],
                'borderColor'     => $color['border'],
                'textColor'       => $color['text'],
                'extendedProps'   => [
                    'estatus'         => $cita->estatus,
                    'especialista'    => $cita->especialista?->nombre_completo,
                    'especialista_id' => $cita->especialista_id,
                    'servicio'        => $cita->servicio?->nombre,
                    'motivo'          => $cita->motivo,
                    'origen'          => $cita->origen,
                ],
            ];
        });

        return response()->json($eventos);
    }

    // ── Guardar nueva cita ────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $data = $this->validar($request);
        if ($this->modoAgenda() !== 'sobrecarga') {
            $this->checkSolapamiento($data);
        }

        $cita = Cita::create($data);

        LogSistemaHelper::logCitas('creada', $cita->id, actual: $data);

        return response()->json([
            'success' => true,
            'cita'    => $cita->load(['especialista.persona', 'paciente.persona', 'servicio']),
            'mensaje' => 'Cita registrada correctamente.',
        ], 201);
    }

    // ── Detalle de cita (para modal) ──────────────────────────────────────────

    public function show(int $id): JsonResponse
    {
        $cita = Cita::with(['especialista.persona', 'paciente.persona', 'sucursal', 'servicio', 'caso'])
            ->findOrFail($id);

        return response()->json($cita);
    }

    // ── Actualizar cita ───────────────────────────────────────────────────────

    public function update(Request $request, int $id): JsonResponse
    {
        $cita = Cita::findOrFail($id);

        $anterior = $cita->only(['especialista_id','paciente_id','fecha','hora_inicio','hora_fin','estatus','motivo','observaciones','servicio_id']);

        $data = $this->validar($request, $id);
        if ($this->modoAgenda() !== 'sobrecarga') {
            $this->checkSolapamiento($data, $id);
        }
        $cita->update($data);

        LogSistemaHelper::logCitas('editada', $cita->id, $anterior, $data);

        return response()->json([
            'success' => true,
            'cita'    => $cita->fresh(['especialista.persona', 'paciente.persona', 'servicio']),
            'mensaje' => 'Cita actualizada correctamente.',
        ]);
    }

    // ── Mover cita (drag & drop) ──────────────────────────────────────────────

    public function mover(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'fecha'       => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin'    => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $cita     = Cita::findOrFail($id);
        $anterior = $cita->only(['fecha', 'hora_inicio', 'hora_fin']);

        $data = [
            'especialista_id' => $cita->especialista_id,
            'fecha'           => $request->fecha,
            'hora_inicio'     => $request->hora_inicio,
            'hora_fin'        => $request->hora_fin,
        ];

        if ($this->modoAgenda() !== 'sobrecarga') {
            $this->checkSolapamiento($data, $id);
        }

        $cita->update([
            'fecha'       => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin'    => $request->hora_fin,
        ]);

        // 'editada' activa _detallesEdicion → compara campo a campo y guarda antes/después
        LogSistemaHelper::logCitas('editada', $cita->id, $anterior, [
            'fecha'       => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin'    => $request->hora_fin,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Cita movida correctamente.',
        ]);
    }

    // ── Cambio rápido de estatus ──────────────────────────────────────────────

    public function cambiarEstatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['estatus' => 'required|in:' . implode(',', self::ESTATUS)]);

        $cita = Cita::findOrFail($id);
        $anterior = $cita->estatus;

        $cita->update(['estatus' => $request->estatus]);

        LogSistemaHelper::logCitas('estatus_cambiado', $cita->id,
            ['estatus' => $anterior], ['estatus' => $request->estatus]);

        return response()->json([
            'success' => true,
            'estatus' => $cita->estatus,
            'mensaje' => 'Estatus actualizado.',
        ]);
    }

    // ── Eliminar ──────────────────────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $cita = Cita::with(['especialista.persona', 'paciente.persona'])->findOrFail($id);
        $descripcion = $cita->nombre_paciente . ' — ' . $cita->fecha->format('d/m/Y') . ' ' . $cita->hora_inicio;

        LogSistemaHelper::logCitas('eliminada', $cita->id, extra: $descripcion);
        $cita->delete();

        return response()->json(['success' => true, 'mensaje' => 'Cita eliminada.']);
    }

    // ── Disponibilidad mensual de un especialista ─────────────────────────────
    // GET /citas/disponibilidad/{id}?mes=YYYY-MM

    public function disponibilidad(int $especialistaId): JsonResponse
    {
        $mes      = request('mes', now()->format('Y-m'));
        $inicio   = Carbon::parse($mes . '-01')->startOfMonth();
        $fin      = $inicio->copy()->endOfMonth();

        $diasMap  = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 0 => 'domingo',
        ];

        // Horarios del especialista indexados por dia_semana
        $horarios = HorarioEspecialista::where('especialista_id', $especialistaId)
            ->get()
            ->keyBy('dia_semana');

        // Citas del mes (no canceladas)
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

            $horario      = $horarios[$diaNombre];
            $slotsTotal   = (int) floor(
                (strtotime($horario->hora_fin) - strtotime($horario->hora_inicio)) / ($horario->duracion_cita * 60)
            );
            $citasDelDia  = (int) ($citasPorDia[$fechaStr] ?? 0);

            $dias[$fechaStr] = $citasDelDia >= $slotsTotal ? 'lleno' : 'disponible';
        }

        return response()->json($dias);
    }

    // ── Slots de horas disponibles para un día concreto ───────────────────────
    // GET /citas/horas-disponibles/{id}?fecha=YYYY-MM-DD

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
        $dia     = $diasMap[$dow];
        $horario = HorarioEspecialista::where('especialista_id', $especialistaId)
            ->where('dia_semana', $dia)
            ->first();

        if (!$horario) {
            return response()->json([]);
        }

        // Generar todos los slots
        $duracion = $horario->duracion_cita;
        $cursor   = Carbon::parse($fecha . ' ' . $horario->hora_inicio);
        $limFin   = Carbon::parse($fecha . ' ' . $horario->hora_fin);
        $slots    = [];

        while ($cursor->copy()->addMinutes($duracion)->lte($limFin)) {
            $hi = $cursor->format('H:i');
            $hf = $cursor->copy()->addMinutes($duracion)->format('H:i');
            $slots[] = ['hora_inicio' => $hi, 'hora_fin' => $hf];
            $cursor->addMinutes($duracion);
        }

        // Citas existentes ese día (no canceladas)
        $citasDelDia = Cita::where('especialista_id', $especialistaId)
            ->where('fecha', $fecha)
            ->whereNotIn('estatus', ['cancelada'])
            ->get(['hora_inicio', 'hora_fin']);

        // Marcar cada slot como disponible o no
        $resultado = array_map(function ($slot) use ($citasDelDia) {
            $hiSlot = strtotime($slot['hora_inicio']);
            $hfSlot = strtotime($slot['hora_fin']);

            $ocupado = $citasDelDia->contains(function ($cita) use ($hiSlot, $hfSlot) {
                return strtotime($cita->hora_inicio) < $hfSlot
                    && strtotime($cita->hora_fin)    > $hiSlot;
            });

            return array_merge($slot, ['disponible' => !$ocupado]);
        }, $slots);

        return response()->json($resultado);
    }

    // ── Confirmación de citas ─────────────────────────────────────────────────

    public function confirmacion(Request $request)
    {
        $especialistas = Especialista::with('persona')->activos()->get();
        $sucursales    = Sucursal::where('estado', true)->get();
        $servicios     = Servicio::where('estado', true)->get();

        return view('modules.citas.confirmacion', compact('especialistas', 'sucursales', 'servicios'));
    }

    public function confirmacionLista(Request $request): JsonResponse
    {
        $busqueda = trim($request->get('q', ''));
        $filtro   = $request->get('filtro', 'pendientes'); // pendientes | confirmadas | rechazadas

        // Mapeo de filtro → estatus
        $estatusPorFiltro = [
            'pendientes'  => ['pendiente'],
            'confirmadas' => ['confirmada'],
            'rechazadas'  => ['cancelada', 'no_asistio'],
        ];
        $estatus = $estatusPorFiltro[$filtro] ?? $estatusPorFiltro['pendientes'];

        $query = Cita::with(['especialista.persona', 'paciente.persona', 'sucursal', 'servicio'])
            ->whereIn('estatus', $estatus)
            ->where('fecha', '>=', now()->toDateString())   // solo vigentes (hoy en adelante)
            ->orderBy('fecha', 'asc')
            ->orderBy('hora_inicio', 'asc');

        if ($busqueda !== '') {
            $query->where(function ($q) use ($busqueda) {
                $q->whereHas('paciente.persona', fn($s) =>
                    $s->where('nombre', 'ilike', "%{$busqueda}%")
                      ->orWhere('apellido', 'ilike', "%{$busqueda}%")
                      ->orWhere('contacto', 'ilike', "%{$busqueda}%")
                      ->orWhere('email', 'ilike', "%{$busqueda}%")
                )
                ->orWhere('nombre_lead', 'ilike', "%{$busqueda}%")
                ->orWhere('telefono_lead', 'ilike', "%{$busqueda}%");
            });
        }

        $paginator = $query->paginate(15);

        return response()->json([
            'data'          => $paginator->items(),
            'next_page_url' => $paginator->nextPageUrl(),
            'current_page'  => $paginator->currentPage(),
            'last_page'     => $paginator->lastPage(),
            'total'         => $paginator->total(),
            'filtro'        => $filtro,
        ]);
    }

    // ── Modo agenda (estricto / sobrecarga) ──────────────────────────────────

    private function modoAgenda(): string
    {
        static $modo = null;
        if ($modo === null) {
            $empresa = \App\Models\Empresa::first();
            $modo = $empresa?->modo_agenda ?? 'estricto';
        }
        return $modo;
    }

    // ── Validación de solapamiento ────────────────────────────────────────────

    private function checkSolapamiento(array $data, ?int $citaId = null): void
    {
        // Canceladas no bloquean agenda
        $query = Cita::where('especialista_id', $data['especialista_id'])
            ->where('fecha', $data['fecha'])
            ->whereNotIn('estatus', ['cancelada'])
            ->where('hora_inicio', '<', $data['hora_fin'])
            ->where('hora_fin',    '>', $data['hora_inicio']);

        if ($citaId) {
            $query->where('id', '!=', $citaId);
        }

        $conflicto = $query->first();

        if ($conflicto) {
            $hi = substr($conflicto->hora_inicio, 0, 5);
            $hf = substr($conflicto->hora_fin,    0, 5);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'hora_inicio' => ["El especialista ya tiene una cita de {$hi} a {$hf} que se solapa con este horario."],
            ]);
        }
    }

    // ── Validación centralizada ───────────────────────────────────────────────

    private function validar(Request $request, ?int $citaId = null): array
    {
        return $request->validate([
            'especialista_id' => 'required|exists:especialistas,id',
            'sucursal_id'     => 'required|exists:sucursales,id',
            'paciente_id'     => 'nullable|exists:pacientes,id',
            'caso_id'         => 'nullable|exists:casos,id',
            'servicio_id'     => 'nullable|exists:servicios,id',
            'nombre_lead'     => 'nullable|string|max:150',
            'telefono_lead'   => 'nullable|string|max:50',
            'fecha'           => 'required|date',
            'hora_inicio'     => 'required|date_format:H:i',
            'hora_fin'        => 'required|date_format:H:i|after:hora_inicio',
            'estatus'         => 'required|in:' . implode(',', self::ESTATUS),
            'motivo'          => 'nullable|string|max:500',
            'observaciones'   => 'nullable|string|max:1000',
            'origen'          => 'nullable|in:' . implode(',', self::ORIGENES),
        ]);
    }
}
