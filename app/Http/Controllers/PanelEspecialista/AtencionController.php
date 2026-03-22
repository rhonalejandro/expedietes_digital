<?php

namespace App\Http\Controllers\PanelEspecialista;

use App\Helpers\LogSistemaHelper;
use App\Http\Controllers\Controller;
use App\Models\Adjunto;
use App\Models\Caso;
use App\Models\Cita;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AtencionController extends Controller
{
    // ── Ver ficha de atención de una cita ────────────────────────────────────

    public function show(int $citaId)
    {
        $especialista = Auth::guard('especialista')->user();

        $cita = Cita::with(['paciente.persona', 'servicio', 'sucursal', 'caso'])
            ->where('especialista_id', $especialista->id)
            ->findOrFail($citaId);

        // Marcar como "en consulta" al abrir la ficha de atención
        if (in_array($cita->estatus, ['pendiente', 'confirmada'])) {
            $anterior = $cita->estatus;
            $cita->update(['estatus' => 'en_consulta']);
            LogSistemaHelper::logCitas('estatus_cambiado', $cita->id,
                ['estatus' => $anterior],
                ['estatus' => 'en_consulta']
            );
        }

        // Historial de casos del paciente atendidos por este especialista
        $casos = collect();
        if ($cita->paciente_id) {
            $casos = Caso::with(['consultas' => fn($q) => $q->with('adjuntos')->orderBy('fecha_hora', 'desc')])
                ->where('paciente_id', $cita->paciente_id)
                ->where('especialista_id', $especialista->id)
                ->orderBy('fecha_apertura', 'desc')
                ->get();
        }

        // Caso actualmente abierto (si existe)
        $casoAbierto = $casos->firstWhere('estado', 'abierto');

        return view('panel_especialista.atencion.show', compact(
            'cita', 'casos', 'casoAbierto', 'especialista'
        ));
    }

    // ── Guardar consulta ─────────────────────────────────────────────────────

    public function guardar(Request $request, int $citaId)
    {
        $especialista = Auth::guard('especialista')->user();

        $cita = Cita::with(['paciente', 'sucursal'])
            ->where('especialista_id', $especialista->id)
            ->findOrFail($citaId);

        $data = $request->validate([
            'accion_caso'    => 'required|in:existente,nuevo',
            'caso_id'        => 'nullable|exists:casos,id',
            'motivo_caso'    => 'nullable|string|max:255',
            'notas_iniciales'=> 'nullable|string',
            'observaciones'  => 'nullable|string',
            'diagnostico'    => 'nullable|string',
            'tratamiento'    => 'nullable|string',
            'indicaciones'   => 'nullable|string',
            'receta'         => 'nullable|string',
            'zonas_afectadas'=> 'nullable|string',
            'fotos'          => 'nullable|array',
            'fotos.*'        => 'file|image|max:5120',
            'fotos_desc'     => 'nullable|array',
            'fotos_desc.*'   => 'nullable|string|max:255',
        ]);

        $consulta = DB::transaction(function () use ($data, $cita, $especialista, $request) {

            // ── Determinar caso ──────────────────────────────────────────────
            if ($data['accion_caso'] === 'existente' && !empty($data['caso_id'])) {
                $casoId = $data['caso_id'];
            } else {
                // Crear nuevo caso
                $caso = Caso::create([
                    'paciente_id'    => $cita->paciente_id,
                    'especialista_id'=> $especialista->id,
                    'sucursal_id'    => $cita->sucursal_id,
                    'motivo'         => $data['motivo_caso'] ?? ($cita->motivo ?? 'Consulta general'),
                    'descripcion'    => $data['motivo_caso'] ?? '',
                    'notas_iniciales'=> $data['notas_iniciales'] ?? null,
                    'fecha_apertura' => now()->toDateString(),
                    'estado'         => 'abierto',
                ]);
                $casoId = $caso->id;
            }

            // Vincular la cita al caso y marcar como atendida
            $estatusAnterior = $cita->estatus;
            $cita->update(['caso_id' => $casoId, 'estatus' => 'atendida']);
            LogSistemaHelper::logCitas('estatus_cambiado', $cita->id,
                ['estatus' => $estatusAnterior],
                ['estatus' => 'atendida']
            );

            // ── Crear consulta ───────────────────────────────────────────────
            $consulta = Consulta::create([
                'caso_id'        => $casoId,
                'cita_id'        => $cita->id,
                'especialista_id'=> $especialista->id,
                'fecha_hora'     => now(),
                'estado'         => 'realizada',
                'observaciones'   => $data['observaciones'] ?? null,
                'diagnostico'     => $data['diagnostico'] ?? null,
                'tratamiento'     => $data['tratamiento'] ?? null,
                'indicaciones'    => $data['indicaciones'] ?? null,
                'receta'          => $data['receta'] ?? null,
                'zonas_afectadas' => !empty($data['zonas_afectadas'])
                    ? json_decode($data['zonas_afectadas'], true)
                    : null,
            ]);

            // ── Subir fotos ──────────────────────────────────────────────────
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $idx => $foto) {
                    $ruta = $foto->store('consultas/fotos', 'public');
                    Adjunto::create([
                        'consulta_id' => $consulta->id,
                        'tipo'        => 'imagen',
                        'ruta'        => $ruta,
                        'descripcion' => $data['fotos_desc'][$idx] ?? null,
                    ]);
                }
            }

            return $consulta;
        });

        $fechaAgenda = is_object($cita->fecha) ? $cita->fecha->toDateString() : $cita->fecha;

        return redirect()
            ->route('panel.agenda', ['fecha' => $fechaAgenda])
            ->with('success', 'Consulta registrada. La cita fue marcada como atendida.');
    }

    // ── Eliminar adjunto (AJAX) ──────────────────────────────────────────────

    public function eliminarFoto(int $id)
    {
        $especialista = Auth::guard('especialista')->user();

        $adjunto = Adjunto::whereHas('consulta', fn($q) =>
            $q->where('especialista_id', $especialista->id)
        )->findOrFail($id);

        Storage::disk('public')->delete($adjunto->ruta);
        $adjunto->delete();

        return response()->json(['success' => true]);
    }
}
