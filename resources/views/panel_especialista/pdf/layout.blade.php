<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
/* ── Reset & Base ─────────────────────────────────── */
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: dejavusans, sans-serif; font-size: 10pt; color: #1a202c; line-height: 1.5; }

/* ── Colores corporativos ─────────────────────────── */
:root { --verde: #6b9158; --verde-claro: #eef4ec; --verde-borde: #b8d1b0; --gris: #64748b; --borde: #e4e8e4; --fondo: #f8faf7; }

/* ── Cabecera del documento (dentro del body) ─────── */
.doc-header { border-bottom: 2px solid #6b9158; padding-bottom: 8px; margin-bottom: 14px; }
.doc-header table { width: 100%; }
.doc-titulo { font-size: 12pt; font-weight: bold; color: #1a202c; }
.doc-subtitulo { font-size: 7.5pt; color: #64748b; margin-top: 2px; }
.doc-badge { display:inline-block; background:#6b9158; color:#fff; font-size:7pt;
    padding: 2px 10px; border-radius: 20px; font-weight: bold; }

/* ── Secciones ────────────────────────────────────── */
.seccion { margin-bottom: 12px; }
.seccion-titulo { font-size: 7pt; font-weight: bold; text-transform: uppercase;
    letter-spacing: 0.09em; color: #4a6e3a; border-bottom: 1px solid #b8d1b0;
    padding-bottom: 2px; margin-bottom: 6px; }

/* ── Tabla de datos del paciente ─────────────────── */
.tabla-datos { width: 100%; border-collapse: collapse; }
.tabla-datos td { padding: 3px 5px; font-size: 8.5pt; vertical-align: top; }
.tabla-datos .lbl { color: #64748b; width: 38%; font-size: 8pt; }
.tabla-datos .val { font-weight: 500; }

/* ── Caja de contenido clínico ───────────────────── */
.caja { background: #f8faf7; border: 1px solid #dde8da; border-left: 3px solid #6b9158;
    border-radius: 3px; padding: 6px 10px; margin-bottom: 8px; }
.caja-titulo { font-size: 7.5pt; font-weight: bold; color: #4a6e3a;
    text-transform: uppercase; letter-spacing: .07em; margin-bottom: 3px; }
.caja-body { font-size: 8.5pt; }
.caja-body p { margin-bottom: 3px; }
.caja-body ul, .caja-body ol { padding-left: 16px; margin-bottom: 3px; }
.caja-body li { margin-bottom: 1px; }
.caja-body h2 { font-size: 9pt; margin-bottom: 3px; color: #1a202c; }
.caja-body h3 { font-size: 8.5pt; margin-bottom: 2px; color: #1a202c; }

/* ── Firma ────────────────────────────────────────── */
.firma-wrap { margin-top: 20px; }
.firma-wrap table { width: 100%; }
.firma-linea { border-top: 1px solid #1a202c; width: 180px; margin-bottom: 3px; }
.firma-nombre { font-size: 8.5pt; font-weight: bold; }
.firma-cargo { font-size: 7.5pt; color: #64748b; }

/* ── Pill / badge ─────────────────────────────────── */
.pill { display:inline-block; background:#eef4ec; color:#4a6e3a; border:1px solid #b8d1b0;
    font-size:7pt; padding:1px 7px; border-radius:20px; margin:1px 2px; }
.pill-rojo { background:#fff5f5; color:#c53030; border-color:#fed7d7; }

/* ── Info bar ─────────────────────────────────────── */
.info-bar { background:#eef4ec; border:1px solid #b8d1b0; border-radius:3px;
    padding:5px 10px; margin-bottom:12px; font-size:8pt; }
.info-bar table { width:100%; }
.info-bar td { padding: 1px 3px; }
</style>
</head>
<body>
@yield('body')
</body>
</html>
