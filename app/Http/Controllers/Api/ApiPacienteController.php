<?php

namespace App\Http\Controllers\Api;

use App\Helpers\TelefonoHelper;
use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Lead;
use App\Models\Paciente;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiPacienteController extends Controller
{
    // ── Buscar paciente por teléfono ──────────────────────────────────────────
    // GET /api/v1/pacientes/buscar-por-telefono?tel=+50769876543

    public function buscarPorTelefono(Request $request): JsonResponse
    {
        $tel = trim($request->query('tel', ''));

        if (!$tel) {
            return response()->json(['error' => 'Falta el parámetro tel.'], 422);
        }

        [$sql, $bindings] = TelefonoHelper::whereColumna('personas.contacto', $tel);

        $persona = Persona::whereRaw($sql, $bindings)->first();

        if (!$persona || !$persona->paciente) {
            // Buscar si existe como lead
            [$sqlL, $bindingsL] = TelefonoHelper::whereColumna('telefono', $tel);
            $lead = Lead::whereRaw($sqlL, $bindingsL)->activos()->first();

            return response()->json([
                'encontrado' => false,
                'lead'       => $lead ? $this->formatLead($lead) : null,
            ]);
        }

        $paciente = $persona->paciente;
        $citas    = $this->citasRecientes($paciente->id);

        return response()->json([
            'encontrado' => true,
            'tipo'       => 'paciente',
            'paciente'   => $this->formatPaciente($paciente, $persona),
            'citas'      => $citas,
        ]);
    }

    // ── Buscar paciente por nombre / identificación ───────────────────────────
    // GET /api/v1/pacientes/buscar?q=texto

    public function buscar(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $pacientes = Paciente::with('persona')
            ->whereHas('persona', fn($s) =>
                $s->where('nombre', 'ilike', "%{$q}%")
                  ->orWhere('apellido', 'ilike', "%{$q}%")
                  ->orWhere('identificacion', 'ilike', "%{$q}%")
                  ->orWhere('contacto', 'ilike', "%{$q}%")
            )
            ->activos()
            ->limit(10)
            ->get();

        return response()->json($pacientes->map(fn($p) => $this->formatPaciente($p, $p->persona)));
    }

    // ── Obtener paciente con citas ─────────────────────────────────────────────
    // GET /api/v1/pacientes/{id}

    public function show(int $id): JsonResponse
    {
        $paciente = Paciente::with('persona')->findOrFail($id);
        $citas    = $this->citasRecientes($id);

        return response()->json([
            'paciente' => $this->formatPaciente($paciente, $paciente->persona),
            'citas'    => $citas,
        ]);
    }

    // ── Crear paciente ────────────────────────────────────────────────────────
    // POST /api/v1/pacientes

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'apellido'            => 'required|string|max:100',
            'tipo_identificacion' => 'required|string|max:50',
            'identificacion'      => 'required|string|max:50|unique:personas,identificacion',
            'contacto'            => 'nullable|string|max:100',
            'email'               => 'nullable|email|max:150',
            'fecha_nacimiento'    => 'nullable|date|before:today',
            'genero'              => 'nullable|in:masculino,femenino,otro',
            'direccion'           => 'nullable|string|max:255',
        ]);

        $paciente = DB::transaction(function () use ($data) {
            $persona  = Persona::create(array_merge($data, ['estado' => true]));
            return Paciente::create(['persona_id' => $persona->id, 'estado' => true]);
        });

        return response()->json([
            'success'  => true,
            'paciente' => $this->formatPaciente($paciente, $paciente->persona),
        ], 201);
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function formatPaciente(Paciente $paciente, Persona $persona): array
    {
        return [
            'id'             => $paciente->id,
            'nombre'         => $persona->nombre . ' ' . $persona->apellido,
            'telefono'       => $persona->contacto,
            'email'          => $persona->email,
            'identificacion' => $persona->identificacion,
            'activo'         => $paciente->estado,
            'url_crm'        => url("/pacientes/{$paciente->id}"),
        ];
    }

    private function formatLead(Lead $lead): array
    {
        return [
            'id'       => $lead->id,
            'nombre'   => $lead->nombre,
            'telefono' => $lead->telefono,
            'email'    => $lead->email,
            'estatus'  => $lead->estatus,
            'origen'   => $lead->origen,
        ];
    }

    private function citasRecientes(int $pacienteId): array
    {
        return Cita::with(['especialista.persona', 'servicio', 'sucursal'])
            ->where('paciente_id', $pacienteId)
            ->where('fecha', '>=', now()->toDateString())
            ->whereNotIn('estatus', ['cancelada'])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get()
            ->map(fn($c) => $this->formatCita($c))
            ->toArray();
    }

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
        ];
    }
}
