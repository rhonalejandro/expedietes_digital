<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Paciente;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiLeadController extends Controller
{
    // ── Listar leads activos ──────────────────────────────────────────────────
    // GET /api/v1/leads

    public function index(Request $request): JsonResponse
    {
        $query = Lead::query();

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        } else {
            $query->activos();
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($s) =>
                $s->where('nombre', 'ilike', "%{$q}%")
                  ->orWhere('telefono', 'ilike', "%{$q}%")
                  ->orWhere('email', 'ilike', "%{$q}%")
            );
        }

        $leads = $query->latest()->paginate(20);

        return response()->json([
            'data'     => $leads->items(),
            'total'    => $leads->total(),
            'page'     => $leads->currentPage(),
            'lastPage' => $leads->lastPage(),
        ]);
    }

    // ── Crear lead rápido ─────────────────────────────────────────────────────
    // POST /api/v1/leads

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:150',
            'telefono'            => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:150',
            'origen'              => 'nullable|in:chatwoot,web,telefono',
            'chatwoot_contact_id' => 'nullable|integer',
            'chatwoot_conv_id'    => 'nullable|integer',
            'notas'               => 'nullable|string|max:1000',
        ]);

        $data['origen']  = $data['origen'] ?? 'chatwoot';
        $data['estatus'] = 'nuevo';

        // Evitar duplicados por chatwoot_contact_id
        if (!empty($data['chatwoot_contact_id'])) {
            $existente = Lead::where('chatwoot_contact_id', $data['chatwoot_contact_id'])
                ->activos()->first();
            if ($existente) {
                return response()->json(['success' => true, 'lead' => $existente, 'existente' => true]);
            }
        }

        $lead = Lead::create($data);

        return response()->json(['success' => true, 'lead' => $lead], 201);
    }

    // ── Convertir lead a paciente ─────────────────────────────────────────────
    // PUT /api/v1/leads/{id}/convertir

    public function convertir(Request $request, int $id): JsonResponse
    {
        $lead = Lead::findOrFail($id);

        if ($lead->estatus === 'convertido') {
            return response()->json(['error' => 'Este lead ya fue convertido.'], 422);
        }

        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'apellido'            => 'required|string|max:100',
            'tipo_identificacion' => 'required|string|max:50',
            'identificacion'      => 'required|string|max:50|unique:personas,identificacion',
            'contacto'            => 'nullable|string|max:100',
            'email'               => 'nullable|email|max:150',
            'fecha_nacimiento'    => 'nullable|date|before:today',
            'genero'              => 'nullable|in:masculino,femenino,otro',
        ]);

        $paciente = DB::transaction(function () use ($data, $lead) {
            $persona  = Persona::create(array_merge($data, ['estado' => true]));
            $paciente = Paciente::create(['persona_id' => $persona->id, 'estado' => true]);

            // Reasignar citas del lead al nuevo paciente
            \App\Models\Cita::where('telefono_lead', $lead->telefono)
                ->whereNull('paciente_id')
                ->update([
                    'paciente_id'   => $paciente->id,
                    'nombre_lead'   => null,
                    'telefono_lead' => null,
                ]);

            // Marcar lead como convertido
            $lead->update([
                'estatus'      => 'convertido',
                'convertido_en' => $paciente->id,
            ]);

            return $paciente;
        });

        return response()->json([
            'success'    => true,
            'paciente_id' => $paciente->id,
            'url_crm'    => url("/pacientes/{$paciente->id}"),
        ]);
    }

    // ── Actualizar estatus de un lead ─────────────────────────────────────────
    // PATCH /api/v1/leads/{id}/estatus

    public function cambiarEstatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'estatus' => 'required|in:nuevo,en_seguimiento,convertido,descartado',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->update(['estatus' => $request->estatus]);

        return response()->json(['success' => true, 'lead' => $lead]);
    }
}
