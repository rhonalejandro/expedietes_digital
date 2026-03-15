<?php

namespace App\Http\Controllers;

use App\Helpers\LogSistemaHelper;
use App\Models\Especialista;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EspecialistaController extends Controller
{
    private const TRATAMIENTOS = ['Dr.', 'Dra.', 'Lic.', 'Lcda.', 'Téc.', 'Téc. Esp.', 'N/A'];

    // ── Listado ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Especialista::with('persona');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('persona', fn($s) =>
                $s->where('nombre', 'ilike', "%{$q}%")
                  ->orWhere('apellido', 'ilike', "%{$q}%")
            )->orWhere('profesion', 'ilike', "%{$q}%")
             ->orWhere('num_colegiado', 'ilike', "%{$q}%");
        }

        if ($request->filled('estado') && in_array($request->estado, ['0', '1'], true)) {
            $query->where('estado', (bool) $request->estado);
        }

        $especialistas = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => Especialista::count(),
            'activos' => Especialista::activos()->count(),
        ];

        return view('modules.especialistas.index', compact('especialistas', 'stats'));
    }

    // ── Detalle ───────────────────────────────────────────────────────────────

    public function show($id)
    {
        $especialista = Especialista::with(['persona', 'sucursales'])->findOrFail($id);
        return view('modules.especialistas.show', compact('especialista'));
    }

    // ── Crear ─────────────────────────────────────────────────────────────────

    public function create()
    {
        return view('modules.especialistas.create', [
            'tratamientos' => self::TRATAMIENTOS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $especialista = DB::transaction(function () use ($data, $request) {
            $persona = Persona::create([
                'nombre'             => $data['nombre'],
                'apellido'           => $data['apellido'],
                'email'              => $data['email_persona'] ?? null,
                'contacto'           => $data['contacto_persona'] ?? null,
                'tipo_identificacion' => 'N/A',
                'identificacion'      => 'N/A',
                'estado'             => true,
            ]);

            $firmaPath = null;
            if ($request->hasFile('firma')) {
                $firmaPath = $request->file('firma')->store('especialistas/firmas', 'public');
            }

            return Especialista::create([
                'persona_id'    => $persona->id,
                'tratamiento'   => $data['tratamiento'],
                'profesion'     => $data['profesion'],
                'especialidad'  => $data['especialidad'] ?? null,
                'num_colegiado' => $data['num_colegiado'] ?? null,
                'telefono'      => $data['telefono'] ?? null,
                'email'         => $data['email'] ?? null,
                'firma'         => $firmaPath,
                'estado'        => true,
            ]);
        });

        LogSistemaHelper::logEspecialistas('creado', $especialista->id, actual: $data);

        return redirect()->route('especialistas.show', $especialista->id)
            ->with('success', 'Especialista registrado exitosamente.');
    }

    // ── Editar ────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $especialista = Especialista::with('persona')->findOrFail($id);
        return view('modules.especialistas.edit', [
            'especialista' => $especialista,
            'tratamientos' => self::TRATAMIENTOS,
        ]);
    }

    public function update(Request $request, $id)
    {
        $especialista = Especialista::with('persona')->findOrFail($id);
        $data = $this->validar($request, $especialista->persona_id);
        $estado = $request->boolean('estado');

        // Capturar valores anteriores ANTES de actualizar
        $anterior = [
            'nombre'        => $especialista->persona->nombre,
            'apellido'      => $especialista->persona->apellido,
            'tratamiento'   => $especialista->tratamiento,
            'profesion'     => $especialista->profesion,
            'especialidad'  => $especialista->especialidad,
            'num_colegiado' => $especialista->num_colegiado,
            'telefono'      => $especialista->telefono,
            'email'         => $especialista->email,
            'estado'        => $especialista->estado,
        ];

        DB::transaction(function () use ($data, $estado, $request, $especialista) {
            $especialista->persona->update([
                'nombre'   => $data['nombre'],
                'apellido' => $data['apellido'],
                'email'    => $data['email_persona'] ?? null,
                'contacto' => $data['contacto_persona'] ?? null,
                'estado'   => $estado,
            ]);

            $firmaPath = $especialista->firma;
            if ($request->hasFile('firma')) {
                if ($firmaPath) Storage::disk('public')->delete($firmaPath);
                $firmaPath = $request->file('firma')->store('especialistas/firmas', 'public');
            }

            $especialista->update([
                'tratamiento'   => $data['tratamiento'],
                'profesion'     => $data['profesion'],
                'especialidad'  => $data['especialidad'] ?? null,
                'num_colegiado' => $data['num_colegiado'] ?? null,
                'telefono'      => $data['telefono'] ?? null,
                'email'         => $data['email'] ?? null,
                'firma'         => $firmaPath,
                'estado'        => $estado,
            ]);
        });

        $actual = [
            'nombre'        => $data['nombre'],
            'apellido'      => $data['apellido'],
            'tratamiento'   => $data['tratamiento'],
            'profesion'     => $data['profesion'],
            'especialidad'  => $data['especialidad'] ?? null,
            'num_colegiado' => $data['num_colegiado'] ?? null,
            'telefono'      => $data['telefono'] ?? null,
            'email'         => $data['email'] ?? null,
            'estado'        => $estado,
        ];
        LogSistemaHelper::logEspecialistas('editado', $especialista->id, $anterior, $actual);

        return redirect()->route('especialistas.show', $especialista->id)
            ->with('success', 'Especialista actualizado exitosamente.');
    }

    // ── Eliminar ──────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $especialista = Especialista::with('persona')->findOrFail($id);

        $nombreCompleto = $especialista->nombre_completo;

        DB::transaction(function () use ($especialista) {
            if ($especialista->firma) {
                Storage::disk('public')->delete($especialista->firma);
            }
            $especialista->persona->delete();
            $especialista->delete();
        });

        LogSistemaHelper::logEspecialistas('eliminado', $especialista->id, extra: $nombreCompleto);

        return redirect()->route('especialistas.index')
            ->with('success', 'Especialista eliminado correctamente.');
    }

    // ── Toggle estado AJAX ────────────────────────────────────────────────────

    public function toggleEstado($id)
    {
        $especialista = Especialista::findOrFail($id);
        $estadoAnterior = $especialista->estado;

        $especialista->update(['estado' => !$especialista->estado]);

        LogSistemaHelper::logEspecialistas('estado_cambiado', $especialista->id,
            ['estado' => $estadoAnterior], ['estado' => $especialista->estado]);

        return response()->json([
            'success' => true,
            'estado'  => $especialista->estado,
            'mensaje' => $especialista->estado ? 'Especialista activado' : 'Especialista desactivado',
        ]);
    }

    // ── Validación centralizada ───────────────────────────────────────────────

    private function validar(Request $request, ?int $personaId = null): array
    {
        return $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'tratamiento'   => 'required|string|max:20',
            'profesion'     => 'required|string|max:100',
            'especialidad'  => 'nullable|string|max:100',
            'num_colegiado' => 'nullable|string|max:50',
            'telefono'      => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:150',
            'email_persona' => 'nullable|email|max:150',
            'contacto_persona' => 'nullable|string|max:100',
            'firma'         => 'nullable|file|image|mimes:png|max:2048',
        ]);
    }
}
