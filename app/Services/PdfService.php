<?php

namespace App\Services;

use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

/**
 * PdfService — Generador centralizado de PDFs con mPDF.
 *
 * USO:
 *   $pdf = PdfService::make('panel_especialista.pdf.consulta', $data, 'Título');
 *   return $pdf->stream('archivo.pdf');       // ver en navegador
 *   return $pdf->download('archivo.pdf');     // descargar
 */
class PdfService
{
    private Mpdf $mpdf;

    private function __construct(Mpdf $mpdf)
    {
        $this->mpdf = $mpdf;
    }

    // ── Factory ──────────────────────────────────────────────────────────────

    public static function make(string $view, array $data = [], string $title = 'Documento'): self
    {
        $empresa = Empresa::first();

        // Logo en base64 para el layout — preferir logo rectangular
        $data['_logoBase64']    = self::_logoBase64($empresa?->logo_rectangular ?? $empresa?->logo);
        $data['_empresa']       = $empresa;
        $data['_generadoEn']    = now()->format('d/m/Y H:i');

        $html = view($view, $data)->render();

        $tmpDir = storage_path('app/mpdf_tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'margin_top'        => 38,
            'margin_bottom'     => 22,
            'margin_left'       => 18,
            'margin_right'      => 18,
            'margin_header'     => 8,
            'margin_footer'     => 6,
            'default_font_size' => 9,
            'default_font'      => 'dejavusans',
            'tempDir'           => $tmpDir,
        ]);

        $mpdf->SetTitle($title);

        // ── Encabezado estándar ───────────────────────────────────────────
        $logoHtml = $data['_logoBase64']
            ? '<img src="' . $data['_logoBase64'] . '" style="height:30px;vertical-align:middle;display:block;margin-bottom:2px">'
            : '<span style="font-size:8pt;font-weight:bold;color:#1a202c">' . htmlspecialchars($empresa?->nombre ?? '') . '</span>';

        $contactoParts = array_filter([
            $empresa?->direccion,
            $empresa?->telefono,
            $empresa?->email,
        ]);
        $contactoHtml = $contactoParts
            ? '<div style="font-size:6pt;color:#94a3b8;margin-top:2px">'
                . implode(' &nbsp;·&nbsp; ', array_map('htmlspecialchars', $contactoParts))
              . '</div>'
            : '';

        $mpdf->SetHTMLHeader('
            <table width="100%" style="border-bottom:1.5px solid #6b9158;padding-bottom:4px;font-family:dejavusans">
                <tr>
                    <td style="vertical-align:middle">'
                        . $logoHtml
                        . $contactoHtml
                    . '</td>
                    <td align="right" style="font-size:6.5pt;color:#94a3b8;vertical-align:middle">'
                        . htmlspecialchars($title)
                    . '</td>
                </tr>
            </table>');

        // ── Pie de página estándar ────────────────────────────────────────
        $mpdf->SetHTMLFooter('
            <table width="100%" style="border-top:1px solid #e2e8f0;padding-top:4px;font-family:dejavusans">
                <tr>
                    <td style="font-size:7pt;color:#94a3b8">
                        Generado el ' . now()->format('d/m/Y \a \l\a\s H:i') . ' · Uso médico confidencial
                    </td>
                    <td align="right" style="font-size:7pt;color:#94a3b8">
                        Página {PAGENO} de {nbpg}
                    </td>
                </tr>
            </table>');

        $mpdf->WriteHTML($html);

        return new self($mpdf);
    }

    // ── Modificadores encadenables ───────────────────────────────────────────

    /** Suprime el encabezado corriente de mPDF (para docs con letterhead propio en el body) */
    public function withoutHeader(): self
    {
        $this->mpdf->SetHTMLHeader('');
        $this->mpdf->SetMargins(18, 18, 14); // top reducido sin header
        return $this;
    }

    // ── Salida ───────────────────────────────────────────────────────────────

    /** Retorna una respuesta inline (se abre en el navegador / iframe) */
    public function stream(string $filename = 'documento.pdf')
    {
        $content = $this->mpdf->Output('', 'S');
        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /** Retorna un StreamedResponse para forzar descarga */
    public function download(string $filename = 'documento.pdf')
    {
        $content = $this->mpdf->Output('', 'S');
        return response()->streamDownload(
            fn() => print($content),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private static function _logoBase64(?string $path): ?string
    {
        if (!$path || !Storage::disk('public')->exists($path)) return null;
        $data = Storage::disk('public')->get($path);
        $mime = Storage::disk('public')->mimeType($path);
        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }

    public static function archivoBase64(string $storagePath): ?string
    {
        return self::_logoBase64($storagePath);
    }
}
