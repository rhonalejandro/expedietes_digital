@extends('panel_especialista.layouts.master')

@section('title', ($paciente->nombre_completo ?? 'Expediente') . ' — Expediente')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/panel_especialista/css/atencion.css') }}?v={{ filemtime(public_path('assets/panel_especialista/css/atencion.css')) }}">
    <style>
        .exp-layout { display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start; }
        @media (max-width: 900px) { .exp-layout { grid-template-columns: 1fr; } }

        /* Perfil lateral */
        .exp-perfil { background: #fff; border: 1px solid #e8ecf0; border-radius: 14px; overflow: hidden; }
        .exp-perfil-top { padding: 2rem 1.5rem 1.5rem; text-align: center; border-bottom: 1px solid #f0f3f7; }
        .exp-avatar { width: 72px; height: 72px; border-radius: 50%; background: rgb(var(--primary)); color: #fff;
            font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
        .exp-nombre { font-size: 1.1rem; font-weight: 700; color: #1a202c; margin-bottom: .25rem; }
        .exp-sub { font-size: .8rem; color: #8a94a6; }
        .exp-stats { display: flex; border-bottom: 1px solid #f0f3f7; }
        .exp-stat { flex: 1; padding: .85rem .5rem; text-align: center; }
        .exp-stat:not(:last-child) { border-right: 1px solid #f0f3f7; }
        .exp-stat-num { font-size: 1rem; font-weight: 700; color: #1a202c; }
        .exp-stat-lbl { font-size: .7rem; color: #8a94a6; }
        .exp-datos { padding: 1.25rem 1.5rem; }
        .exp-section-lbl { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #8a94a6; margin-bottom: .85rem; }
        .exp-campo { display: flex; gap: .75rem; align-items: flex-start; margin-bottom: .85rem; }
        .exp-campo i { font-size: 1rem; color: rgb(var(--primary)); margin-top: .1rem; flex-shrink: 0; }
        .exp-campo-lbl { font-size: .7rem; color: #8a94a6; line-height: 1.2; }
        .exp-campo-val { font-size: .85rem; color: #1a202c; font-weight: 500; line-height: 1.3; }
        .exp-back { display: inline-flex; align-items: center; gap: .4rem; font-size: .82rem; color: #64748b;
            text-decoration: none; margin-bottom: 1.25rem; }
        .exp-back:hover { color: rgb(var(--primary)); }

        /* Casos */
        .exp-casos { background: #fff; border: 1px solid #e8ecf0; border-radius: 14px; overflow: hidden; }
        .exp-casos-header { padding: 1.1rem 1.5rem; border-bottom: 1px solid #f0f3f7; display: flex; align-items: center; gap: .6rem; }
        .exp-casos-header h6 { margin: 0; font-weight: 700; font-size: .95rem; color: #1a202c; }
        .exp-casos-count { font-size: .72rem; font-weight: 700; background: rgb(var(--primary) / .1); color: rgb(var(--primary)); padding: .15rem .55rem; border-radius: 20px; }
        .exp-empty { padding: 3rem 1.5rem; text-align: center; color: #8a94a6; }
        .exp-empty i { font-size: 2rem; display: block; margin-bottom: .5rem; }

        /* Fotos — thumbnail clickeable */
        .exp-foto-thumb { display: inline-block; cursor: zoom-in; }
        .exp-foto-thumb img { border-radius: 6px; }

        /* Lightbox */
        .exp-lightbox { display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.88); align-items: center; justify-content: center;
            flex-direction: column; padding: 1.5rem; gap: 1rem; }
        .exp-lightbox.show { display: flex; }
        .exp-lightbox-inner { position: relative; text-align: center; }
        .exp-lightbox-main { max-width: 80vw; max-height: 70vh; border-radius: 10px;
            box-shadow: 0 8px 40px rgba(0,0,0,.6); display: block; object-fit: contain; }
        .exp-lightbox-close { position: absolute; top: -14px; right: -14px; width: 32px; height: 32px;
            background: #fff; border: none; border-radius: 50%; font-size: 1rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,.3); }
        .exp-lightbox-desc { font-size: .8rem; color: rgba(255,255,255,.75); text-align: center; min-height: 1.2em; }
        /* Strip de thumbnails */
        .exp-lightbox-strip { display: flex; gap: .5rem; justify-content: center; flex-wrap: wrap; max-width: 80vw; }
        .exp-lb-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 6px;
            border: 2px solid transparent; cursor: pointer; opacity: .55; transition: opacity .15s, border-color .15s; }
        .exp-lb-thumb:hover { opacity: .85; }
        .exp-lb-thumb.active { opacity: 1; border-color: #fff; }

        /* Visor de zonas read-only */
        .zv-wrap { display: flex; gap: .75rem; flex-wrap: wrap; margin-top: .5rem; }
        .zv-pie { width: 237px; flex-shrink: 0; }

        /* Zona + fotos lado a lado */
        .zv-fotos-row { display: flex; gap: 1rem; align-items: flex-start; flex-wrap: wrap; margin-top: .5rem; }
        .zv-fotos-row .atc-consulta-fotos { display: flex; flex-direction: column; gap: .4rem; margin-top: 0; }
        .zv-fotos-row .atc-consulta-fotos a img { width: 110px; height: 110px; object-fit: cover; }
        .zv-pie-titulo { font-size: .72rem; font-weight: 700; color: #64748b; text-align: center;
            margin-bottom: .35rem; text-transform: uppercase; letter-spacing: .05em; }
        .zv-inactivo { opacity: .15 !important; cursor: default !important; }

        /* Botón ver PDF */
        .btn-ver-pdf { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem;
            padding:.2rem .65rem; border:1px solid #b8d1b0; background:#eef4ec; color:#4a6e3a;
            border-radius:6px; cursor:pointer; font-family:inherit; transition:background .15s; }
        .btn-ver-pdf:hover { background:#dcebd7; }
        .btn-ver-pdf i { font-size:.9rem; color:#6b9158; }

        /* Modal PDF */
        .pdf-modal-backdrop { display:none; position:fixed; inset:0; z-index:10000;
            background:rgba(15,20,25,.75); align-items:center; justify-content:center; padding:1rem; }
        .pdf-modal-backdrop.show { display:flex; }
        .pdf-modal { background:#fff; border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,.4);
            display:flex; flex-direction:column; width:min(880px,96vw); height:90vh; overflow:hidden; }
        .pdf-modal-header { display:flex; align-items:center; justify-content:space-between;
            padding:.75rem 1.25rem; border-bottom:1px solid #e4e8e4; flex-shrink:0; }
        .pdf-modal-title { font-weight:700; font-size:.9rem; color:#1a202c;
            display:flex; align-items:center; gap:.4rem; }
        .pdf-modal-title i { color:#6b9158; }
        .pdf-modal-actions { display:flex; align-items:center; gap:.5rem; }
        .pdf-modal-btn { display:inline-flex; align-items:center; gap:.3rem; font-size:.78rem;
            padding:.3rem .8rem; border-radius:6px; cursor:pointer; border:none; font-family:inherit; }
        .pdf-modal-btn-download { background:#6b9158; color:#fff; }
        .pdf-modal-btn-download:hover { background:#5a7d49; }
        .pdf-modal-btn-close { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
        .pdf-modal-btn-close:hover { background:#e2e8f0; }
        .pdf-modal-iframe { flex:1; width:100%; border:none; display:block; }
        .zona-hotspot.activo { background: rgb(var(--primary)) !important; border-color: rgb(var(--primary)) !important; }
        /* Tooltip read-only */
        .zv-wrap .zona-hotspot::before { content: attr(data-label); position: absolute;
            bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%);
            background: #1a202c; color: #fff; font-size: .65rem; white-space: nowrap;
            padding: 2px 7px; border-radius: 4px; pointer-events: none; opacity: 0; transition: opacity .15s; z-index: 10; }
        .zv-wrap .zona-hotspot.activo:hover::before { opacity: 1; }
    </style>
@endpush

@section('content')

<a href="{{ url()->previous() }}" class="exp-back">
    <i class="ti ti-arrow-left"></i> Volver
</a>

@php
    $persona = $paciente->persona;
    $iniciales = strtoupper(substr($persona->nombre ?? '?', 0, 1)) . strtoupper(substr($persona->apellido ?? '', 0, 1));
    $edad = $persona->fecha_nacimiento
        ? \Carbon\Carbon::parse($persona->fecha_nacimiento)->age . ' años'
        : null;
@endphp

<div class="exp-layout">

    {{-- ── Perfil izquierdo ────────────────────────────────── --}}
    <div class="exp-perfil">

        <div class="exp-perfil-top">
            <div class="exp-avatar">{{ $iniciales }}</div>
            <div class="exp-nombre">{{ $paciente->nombre_completo }}</div>
            @if($edad)
                <div class="exp-sub">{{ $edad }}</div>
            @endif
        </div>

        <div class="exp-stats">
            <div class="exp-stat">
                <div class="exp-stat-num">{{ $paciente->casos->count() }}</div>
                <div class="exp-stat-lbl">Casos</div>
            </div>
            <div class="exp-stat">
                <div class="exp-stat-num">{{ $paciente->citas->count() }}</div>
                <div class="exp-stat-lbl">Citas</div>
            </div>
        </div>

        <div class="exp-datos">
            <div class="exp-section-lbl">Datos Personales</div>

            @if($persona->identificacion)
            <div class="exp-campo">
                <i class="ti ti-credit-card"></i>
                <div>
                    <div class="exp-campo-lbl">Identificación</div>
                    <div class="exp-campo-val">{{ $persona->tipo_identificacion ? $persona->tipo_identificacion.' ' : '' }}{{ $persona->identificacion }}</div>
                </div>
            </div>
            @endif

            @if($persona->fecha_nacimiento)
            <div class="exp-campo">
                <i class="ti ti-cake"></i>
                <div>
                    <div class="exp-campo-lbl">Fecha de nacimiento</div>
                    <div class="exp-campo-val">{{ \Carbon\Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') }} ({{ $edad }})</div>
                </div>
            </div>
            @endif

            @if($persona->genero)
            <div class="exp-campo">
                <i class="ti ti-gender-bigender"></i>
                <div>
                    <div class="exp-campo-lbl">Género</div>
                    <div class="exp-campo-val">{{ ucfirst($persona->genero) }}</div>
                </div>
            </div>
            @endif

            @if($persona->nacionalidad)
            <div class="exp-campo">
                <i class="ti ti-world"></i>
                <div>
                    <div class="exp-campo-lbl">Nacionalidad</div>
                    <div class="exp-campo-val">{{ $persona->nacionalidad }}</div>
                </div>
            </div>
            @endif

            @if($persona->ocupacion)
            <div class="exp-campo">
                <i class="ti ti-briefcase"></i>
                <div>
                    <div class="exp-campo-lbl">Ocupación</div>
                    <div class="exp-campo-val">{{ $persona->ocupacion }}</div>
                </div>
            </div>
            @endif

            @if($persona->seguro_medico)
            <div class="exp-campo">
                <i class="ti ti-heart-rate-monitor"></i>
                <div>
                    <div class="exp-campo-lbl">Seguro médico</div>
                    <div class="exp-campo-val">{{ $persona->seguro_medico }}</div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ── Casos / Expedientes ──────────────────────────────── --}}
    <div class="exp-casos">
        <div class="exp-casos-header">
            <i class="ti ti-folder-open" style="color: rgb(var(--primary))"></i>
            <h6>Expediente Clínico</h6>
            <span class="exp-casos-count">{{ $paciente->casos->count() }}</span>
        </div>

        @if($paciente->casos->isEmpty())
            <div class="exp-empty">
                <i class="ti ti-folder-off"></i>
                <p class="mb-0">Sin casos registrados</p>
            </div>
        @else
            <div class="accordion atc-accordion" id="accordionExpCasos">
                @foreach($paciente->casos as $idx => $caso)
                <div class="atc-accordion-item">
                    <button class="atc-accordion-btn {{ $idx > 0 ? 'collapsed' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#exp-caso-{{ $caso->id }}"
                            aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}">
                        <div class="atc-acc-left">
                            <span class="atc-acc-dot atc-acc-dot--{{ $caso->estado }}"></span>
                            <div>
                                <div class="atc-acc-title">{{ \Illuminate\Support\Str::limit($caso->motivo ?? 'Caso #'.$caso->id, 60) }}</div>
                                <div class="atc-acc-sub">
                                    {{ \Carbon\Carbon::parse($caso->fecha_apertura)->isoFormat('D MMM YYYY') }}
                                    &nbsp;·&nbsp;{{ $caso->consultas->count() }} consulta(s)
                                    &nbsp;·&nbsp;<span class="badge {{ $caso->estado === 'abierto' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}" style="font-size:.65rem">{{ ucfirst($caso->estado) }}</span>
                                </div>
                            </div>
                        </div>
                        <i class="ti ti-chevron-down atc-acc-chevron"></i>
                    </button>

                    <div id="exp-caso-{{ $caso->id }}" class="collapse {{ $idx === 0 ? 'show' : '' }}">
                        <div class="atc-acc-body">

                            @if($caso->notas_iniciales)
                                <p class="atc-hist-notas">{{ $caso->notas_iniciales }}</p>
                            @endif

                            @forelse($caso->consultas as $consulta)
                                <div class="atc-consulta-item">
                                    <div class="atc-consulta-fecha" style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                                        <span>
                                            <i class="ti ti-calendar-event"></i>
                                            {{ \Carbon\Carbon::parse($consulta->fecha_hora)->isoFormat('D MMM YYYY, HH:mm') }}
                                        </span>
                                        <div style="display:flex;gap:.35rem">
                                            <button type="button"
                                                    class="btn-ver-pdf"
                                                    data-pdf-url="{{ route('panel.paciente.consulta.pdf', ['pacienteId' => $paciente->id, 'consultaId' => $consulta->id]) }}"
                                                    style="font-size:.7rem;padding:.2rem .6rem;display:inline-flex;align-items:center;gap:.25rem;border:1px solid #198754;background:#198754;color:#fff;border-radius:5px;cursor:pointer;">
                                                <i class="ti ti-file-type-pdf"></i> Ficha
                                            </button>
                                            @if($consulta->receta || $consulta->indicaciones)
                                            <button type="button"
                                                    class="btn-ver-pdf"
                                                    data-pdf-url="{{ route('panel.paciente.consulta.receta', ['pacienteId' => $paciente->id, 'consultaId' => $consulta->id]) }}"
                                                    style="font-size:.7rem;padding:.2rem .6rem;display:inline-flex;align-items:center;gap:.25rem;border:1px solid #ffc107;background:#ffc107;color:#000;border-radius:5px;cursor:pointer;">
                                                <i class="ti ti-prescription"></i> Receta
                                            </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if($consulta->observaciones)
                                        <div class="atc-consulta-field">
                                            <span class="atc-field-lbl">Observaciones:</span>
                                            {!! $consulta->observaciones !!}
                                        </div>
                                    @endif
                                    @if($consulta->diagnostico)
                                        <div class="atc-consulta-field">
                                            <span class="atc-field-lbl">Diagnóstico:</span>
                                            {!! $consulta->diagnostico !!}
                                        </div>
                                    @endif
                                    @if($consulta->tratamiento)
                                        <div class="atc-consulta-field">
                                            <span class="atc-field-lbl">Tratamiento:</span>
                                            {!! $consulta->tratamiento !!}
                                        </div>
                                    @endif
                                    @if($consulta->indicaciones)
                                        <div class="atc-consulta-field">
                                            <span class="atc-field-lbl">Indicaciones:</span>
                                            {!! $consulta->indicaciones !!}
                                        </div>
                                    @endif
                                    @if($consulta->receta)
                                        <div class="atc-consulta-field">
                                            <span class="atc-field-lbl">Receta:</span>
                                            {!! $consulta->receta !!}
                                        </div>
                                    @endif

                                    @if(!empty($consulta->zonas_afectadas))
                                        @php
                                            $zonaLabels = [
                                                'talon'=>'Talón','planta'=>'Planta','arco'=>'Arco plantar','antepi'=>'Antepié',
                                                'tobillo'=>'Tobillo','dorso'=>'Dorso',
                                                'dedo_1'=>'Hallux','dedo_2'=>'Índice','dedo_3'=>'Medio','dedo_4'=>'Anular','dedo_5'=>'Meñique',
                                                'uña_1'=>'Uña Hallux','uña_2'=>'Uña Índice','uña_3'=>'Uña Medio','uña_4'=>'Uña Anular','uña_5'=>'Uña Meñique',
                                            ];
                                        @endphp
                                        <div class="atc-consulta-field mt-2">
                                            <span class="atc-field-lbl">Zonas afectadas:</span>
                                            @foreach(['derecho','izquierdo'] as $side)
                                                @if(!empty($consulta->zonas_afectadas[$side]))
                                                    <span class="badge bg-light text-secondary border me-1" style="font-size:.68rem;">
                                                        {{ $side === 'izquierdo' ? 'Pie Izq.' : 'Pie Der.' }}:
                                                        {{ implode(', ', array_map(fn($z) => $zonaLabels[$z] ?? $z, $consulta->zonas_afectadas[$side])) }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="zv-fotos-row">
                                            @include('panel_especialista._partials.zonas-viewer', [
                                                'zonasAfectadas' => $consulta->zonas_afectadas
                                            ])
                                            @if($consulta->adjuntos->count())
                                                <div class="atc-consulta-fotos" style="margin-top:0;align-self:center;">
                                                    @foreach($consulta->adjuntos->where('tipo','imagen') as $adj)
                                                    @php $url = \Illuminate\Support\Facades\Storage::url($adj->ruta); @endphp
                                                        <a href="{{ $url }}" class="exp-foto-thumb"
                                                           data-src="{{ $url }}"
                                                           data-desc="{{ $adj->descripcion }}"
                                                           data-group="consulta-{{ $consulta->id }}">
                                                            <img src="{{ $url }}" alt="{{ $adj->descripcion }}">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($consulta->adjuntos->count())
                                        <div class="atc-consulta-fotos">
                                            @foreach($consulta->adjuntos->where('tipo','imagen') as $adj)
                                            @php $url = \Illuminate\Support\Facades\Storage::url($adj->ruta); @endphp
                                                <a href="{{ $url }}" class="exp-foto-thumb"
                                                   data-src="{{ $url }}"
                                                   data-desc="{{ $adj->descripcion }}"
                                                   data-group="consulta-{{ $consulta->id }}">
                                                    <img src="{{ $url }}" alt="{{ $adj->descripcion }}">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Sin consultas en este caso.</p>
                            @endforelse

                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- Modal PDF --}}
<div class="pdf-modal-backdrop" id="pdf-modal-backdrop">
    <div class="pdf-modal">
        <div class="pdf-modal-header">
            <div class="pdf-modal-title">
                <i class="ti ti-file-type-pdf"></i>
                <span id="pdf-modal-title-text">Ficha Médica</span>
            </div>
            <div class="pdf-modal-actions">
                <a id="pdf-modal-download" href="#" download
                   class="pdf-modal-btn pdf-modal-btn-download">
                    <i class="ti ti-download"></i> Descargar
                </a>
                <button class="pdf-modal-btn pdf-modal-btn-close" id="pdf-modal-close">
                    <i class="ti ti-x"></i> Cerrar
                </button>
            </div>
        </div>
        <iframe id="pdf-modal-iframe" class="pdf-modal-iframe" src="about:blank"></iframe>
    </div>
</div>

{{-- Lightbox --}}
<div class="exp-lightbox" id="exp-lightbox">
    <div class="exp-lightbox-inner">
        <button class="exp-lightbox-close" id="exp-lightbox-close" title="Cerrar">
            <i class="ti ti-x"></i>
        </button>
        <img src="" id="exp-lightbox-img" class="exp-lightbox-main" alt="">
    </div>
    <div class="exp-lightbox-desc" id="exp-lightbox-desc"></div>
    <div class="exp-lightbox-strip" id="exp-lightbox-strip"></div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Modal PDF ──────────────────────────────────────────────
    const pdfBackdrop  = document.getElementById('pdf-modal-backdrop');
    const pdfIframe    = document.getElementById('pdf-modal-iframe');
    const pdfDownload  = document.getElementById('pdf-modal-download');
    const pdfClose     = document.getElementById('pdf-modal-close');

    function openPdfModal(url) {
        pdfIframe.src      = url;
        pdfDownload.href   = url + '?dl=1';
        pdfBackdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closePdfModal() {
        pdfBackdrop.classList.remove('show');
        pdfIframe.src = 'about:blank';
        document.body.style.overflow = '';
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-ver-pdf');
        if (!btn) return;
        openPdfModal(btn.dataset.pdfUrl);
    });
    pdfClose.addEventListener('click', closePdfModal);
    pdfBackdrop.addEventListener('click', (e) => { if (e.target === pdfBackdrop) closePdfModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closePdfModal(); });

    // ── Lightbox fotos ─────────────────────────────────────────
    const lb      = document.getElementById('exp-lightbox');
    const lbImg   = document.getElementById('exp-lightbox-img');
    const lbDesc  = document.getElementById('exp-lightbox-desc');
    const lbStrip = document.getElementById('exp-lightbox-strip');
    const lbClose = document.getElementById('exp-lightbox-close');

    function _show(src, desc, group) {
        lbImg.src          = src;
        lbDesc.textContent = desc || '';

        // Construir strip con todas las fotos del grupo
        lbStrip.innerHTML = '';
        if (group) {
            const siblings = document.querySelectorAll(`.exp-foto-thumb[data-group="${group}"]`);
            if (siblings.length > 1) {
                siblings.forEach(s => {
                    const t = document.createElement('img');
                    t.src       = s.dataset.src;
                    t.className = 'exp-lb-thumb' + (s.dataset.src === src ? ' active' : '');
                    t.addEventListener('click', () => _show(s.dataset.src, s.dataset.desc, group));
                    lbStrip.appendChild(t);
                });
            }
        }

        lb.classList.add('show');
    }

    document.addEventListener('click', function (e) {
        const thumb = e.target.closest('.exp-foto-thumb');
        if (!thumb) return;
        e.preventDefault();
        _show(thumb.dataset.src, thumb.dataset.desc, thumb.dataset.group);
    });

    lbClose.addEventListener('click', () => lb.classList.remove('show'));
    lb.addEventListener('click', (e) => { if (e.target === lb) lb.classList.remove('show'); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') lb.classList.remove('show'); });
});
</script>
@endpush
