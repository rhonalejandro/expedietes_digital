<?php

namespace App\Http\Controllers;

use App\Helpers\LogSistemaHelper;
use App\Models\Paciente;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{
    // ── Listado ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Paciente::with('persona');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('persona', fn($sub) =>
                $sub->where('nombre', 'ilike', "%{$q}%")
                    ->orWhere('apellido', 'ilike', "%{$q}%")
                    ->orWhere('identificacion', 'ilike', "%{$q}%")
            );
        }

        if ($request->filled('estado') && in_array($request->estado, ['0', '1'], true)) {
            $query->where('estado', (bool) $request->estado);
        }

        $pacientes = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => Paciente::count(),
            'activos' => Paciente::activos()->count(),
            'nuevos'  => Paciente::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count(),
        ];

        return view('modules.pacientes.index', compact('pacientes', 'stats'));
    }

    // ── Detalle ───────────────────────────────────────────────────────────────

    public function show($id)
    {
        $paciente = Paciente::with(['persona', 'casos', 'citas'])->findOrFail($id);

        return view('modules.pacientes.show', compact('paciente'));
    }

    // ── Crear ─────────────────────────────────────────────────────────────────

    public function create()
    {
        return view('modules.pacientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'apellido'            => 'required|string|max:100',
            'tipo_identificacion' => 'required|string|max:50',
            'identificacion'      => 'required|string|max:50|unique:personas,identificacion',
            'fecha_nacimiento'    => 'nullable|date|before:today',
            'email'               => 'nullable|email|max:150',
            'contacto'            => 'nullable|string|max:100',
            'direccion'           => 'nullable|string|max:255',
            'genero'              => 'nullable|in:masculino,femenino,otro',
            'ocupacion'           => 'nullable|string|max:100',
            'nacionalidad'        => 'nullable|string|max:50',
            'seguro_medico'       => 'nullable|string|max:100',
            'contacto_emergencia' => 'nullable|string|max:100',
        ]);

        $paciente = DB::transaction(function () use ($data) {
            $persona = Persona::create(array_merge($data, ['estado' => true]));

            return Paciente::create([
                'persona_id' => $persona->id,
                'estado'     => true,
            ]);
        });

        LogSistemaHelper::logPacientes('creado', $paciente->id, actual: $data);

        return redirect()->route('pacientes.show', $paciente->id)
            ->with('success', 'Paciente registrado exitosamente.');
    }

    // ── Editar ────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $paciente = Paciente::with('persona')->findOrFail($id);

        return view('modules.pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, $id)
    {
        $paciente = Paciente::with('persona')->findOrFail($id);

        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'apellido'            => 'required|string|max:100',
            'tipo_identificacion' => 'required|string|max:50',
            'identificacion'      => 'required|string|max:50|unique:personas,identificacion,' . $paciente->persona_id,
            'fecha_nacimiento'    => 'nullable|date|before:today',
            'email'               => 'nullable|email|max:150',
            'contacto'            => 'nullable|string|max:100',
            'direccion'           => 'nullable|string|max:255',
            'genero'              => 'nullable|in:masculino,femenino,otro',
            'ocupacion'           => 'nullable|string|max:100',
            'nacionalidad'        => 'nullable|string|max:50',
            'seguro_medico'       => 'nullable|string|max:100',
            'contacto_emergencia' => 'nullable|string|max:100',
        ]);

        $estado = $request->boolean('estado');

        // Capturar valores anteriores ANTES de actualizar
        $anterior = $paciente->persona->only(array_keys($data));
        $anterior['estado'] = $paciente->estado;

        DB::transaction(function () use ($data, $estado, $paciente) {
            $paciente->persona->update(array_merge($data, ['estado' => $estado]));
            $paciente->update(['estado' => $estado]);
        });

        // Registrar log con todos los cambios campo por campo
        $actual = array_merge($data, ['estado' => $estado]);
        LogSistemaHelper::logPacientes('editado', $paciente->id, $anterior, $actual);

        return redirect()->route('pacientes.show', $paciente->id)
            ->with('success', 'Paciente actualizado exitosamente.');
    }

    // ── Eliminar (soft delete) ─────────────────────────────────────────────────

    public function destroy($id)
    {
        $paciente = Paciente::with('persona')->findOrFail($id);
        $nombreCompleto = $paciente->persona->nombre . ' ' . $paciente->persona->apellido;

        DB::transaction(function () use ($paciente) {
            $paciente->persona->delete();
            $paciente->delete();
        });

        LogSistemaHelper::logPacientes('eliminado', $paciente->id, extra: $nombreCompleto);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }

    // ── Búsqueda rápida (autocomplete) ───────────────────────────────────────
    // GET /pacientes/buscar?q=texto  →  JSON [{id, nombre, telefono, email}]

    public function buscar(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $pacientes = Paciente::with('persona')
            ->whereHas('persona', fn($sub) =>
                $sub->where('nombre',   'ilike', "%{$q}%")
                    ->orWhere('apellido', 'ilike', "%{$q}%")
                    ->orWhere('contacto', 'ilike', "%{$q}%")
                    ->orWhere('email',    'ilike', "%{$q}%")
            )
            ->where('estado', true)
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'      => $p->id,
                'nombre'  => trim($p->persona->nombre . ' ' . $p->persona->apellido),
                'telefono'=> $p->persona->contacto ?? '',
                'email'   => $p->persona->email    ?? '',
            ]);

        return response()->json($pacientes);
    }

    // ── Toggle Estado (AJAX) ──────────────────────────────────────────────────

    public function toggleEstado($id)
    {
        $paciente = Paciente::findOrFail($id);
        $estadoAnterior = $paciente->estado;

        $paciente->update(['estado' => !$paciente->estado]);

        LogSistemaHelper::logPacientes('estado_cambiado', $paciente->id,
            ['estado' => $estadoAnterior], ['estado' => $paciente->estado]);

        return response()->json([
            'success' => true,
            'estado'  => $paciente->estado,
            'mensaje' => $paciente->estado ? 'Paciente activado' : 'Paciente desactivado',
        ]);
    }
}
