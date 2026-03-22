<?php

namespace App\Http\Controllers\PanelEspecialista;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Services\PdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function consultaPdf(int $pacienteId, int $consultaId)
    {
        $especialista = Auth::guard('especialista')->user();

        $consulta = Consulta::with(['adjuntos', 'caso.paciente.persona'])
            ->whereHas('caso', fn($q) => $q
                ->where('paciente_id', $pacienteId)
                ->where('especialista_id', $especialista->id)
            )
            ->findOrFail($consultaId);

        $paciente = $consulta->caso->paciente;
        $caso     = $consulta->caso;

        // Firma del especialista en base64
        $firmaBase64 = null;
        if ($especialista->firma && Storage::disk('public')->exists($especialista->firma)) {
            $firmaData   = Storage::disk('public')->get($especialista->firma);
            $firmaMime   = Storage::disk('public')->mimeType($especialista->firma);
            $firmaBase64 = 'data:' . $firmaMime . ';base64,' . base64_encode($firmaData);
        }

        // Fotos de la consulta en base64 para incrustar en el PDF
        $fotosBase64 = $consulta->adjuntos
            ->where('tipo', 'imagen')
            ->map(function ($adj) {
                if (!Storage::disk('public')->exists($adj->ruta)) return null;
                $data = Storage::disk('public')->get($adj->ruta);
                $mime = Storage::disk('public')->mimeType($adj->ruta);
                return [
                    'src'        => 'data:' . $mime . ';base64,' . base64_encode($data),
                    'descripcion'=> $adj->descripcion,
                ];
            })
            ->filter()
            ->values();

        $filename = 'ficha_' . $paciente->id . '_' . now()->format('Ymd') . '.pdf';
        $title    = 'Ficha Médica — ' . $paciente->nombre_completo;

        return PdfService::make('panel_especialista.pdf.consulta', [
            'consulta'    => $consulta,
            'paciente'    => $paciente,
            'caso'        => $caso,
            'especialista'=> $especialista,
            'firmaBase64' => $firmaBase64,
            'fotosBase64' => $fotosBase64,
        ], $title)->stream($filename);
    }

    public function recetaPdf(int $pacienteId, int $consultaId)
    {
        $especialista = Auth::guard('especialista')->user();

        $consulta = Consulta::with(['caso.paciente.persona'])
            ->whereHas('caso', fn($q) => $q
                ->where('paciente_id', $pacienteId)
                ->where('especialista_id', $especialista->id)
            )
            ->findOrFail($consultaId);

        $paciente = $consulta->caso->paciente;

        $firmaBase64 = null;
        if ($especialista->firma && Storage::disk('public')->exists($especialista->firma)) {
            $firmaData   = Storage::disk('public')->get($especialista->firma);
            $firmaMime   = Storage::disk('public')->mimeType($especialista->firma);
            $firmaBase64 = 'data:' . $firmaMime . ';base64,' . base64_encode($firmaData);
        }

        $filename = 'receta_' . $paciente->id . '_' . now()->format('Ymd') . '.pdf';

        return PdfService::make('panel_especialista.pdf.receta', [
            'consulta'    => $consulta,
            'paciente'    => $paciente,
            'caso'        => $consulta->caso,
            'especialista'=> $especialista,
            'firmaBase64' => $firmaBase64,
        ], 'Receta Médica — ' . $paciente->nombre_completo)->stream($filename);
    }
}
