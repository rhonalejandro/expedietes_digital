<div class="atc-card">
    <div class="atc-card-header">
        <i class="ti ti-stethoscope me-2"></i> Nueva Consulta
    </div>
    <div class="atc-card-body">

        <form action="{{ route('panel.atencion.guardar', $cita->id) }}"
              method="POST"
              enctype="multipart/form-data"
              id="form-consulta">
            @csrf

            {{-- ── Sección: Caso clínico ─────────────────────────────── --}}
            <div class="atc-section-title">
                <i class="ti ti-folder-open"></i> Caso Clínico
            </div>

            {{-- Selección: caso existente o nuevo --}}
            <div class="atc-caso-toggle mb-3" id="caso-toggle">
                @if($casoAbierto)
                    <label class="atc-caso-opt atc-caso-opt--active" id="opt-existente">
                        <input type="radio" name="accion_caso" value="existente" checked hidden>
                        <i class="ti ti-folder-open"></i>
                        <div>
                            <div class="atc-caso-opt-title">Caso abierto</div>
                            <div class="atc-caso-opt-sub">{{ \Illuminate\Support\Str::limit($casoAbierto->motivo ?? 'Sin motivo', 45) }}</div>
                        </div>
                        <i class="ti ti-circle-check atc-caso-opt-check"></i>
                    </label>

                    <label class="atc-caso-opt" id="opt-nuevo">
                        <input type="radio" name="accion_caso" value="nuevo" hidden>
                        <i class="ti ti-folder-plus"></i>
                        <div>
                            <div class="atc-caso-opt-title">Abrir nuevo caso</div>
                            <div class="atc-caso-opt-sub">Registrar un problema diferente</div>
                        </div>
                    </label>
                @else
                    <label class="atc-caso-opt atc-caso-opt--active" id="opt-nuevo" style="width:100%">
                        <input type="radio" name="accion_caso" value="nuevo" checked hidden>
                        <i class="ti ti-folder-plus"></i>
                        <div>
                            <div class="atc-caso-opt-title">Primer caso del paciente</div>
                            <div class="atc-caso-opt-sub">Se creará un nuevo caso clínico</div>
                        </div>
                        <i class="ti ti-circle-check atc-caso-opt-check"></i>
                    </label>
                @endif
            </div>

            {{-- caso_id oculto (si hay caso abierto) --}}
            @if($casoAbierto)
                <input type="hidden" name="caso_id" id="caso-id-input" value="{{ $casoAbierto->id }}">
            @endif

            {{-- Campos solo si es nuevo caso --}}
            <div id="nuevo-caso-fields" class="{{ $casoAbierto ? 'd-none' : '' }}">
                <div class="row g-2 mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Motivo del caso <span class="text-danger">*</span></label>
                        <textarea name="motivo_caso" id="ta-motivo_caso" class="d-none">{{ old('motivo_caso', $cita->motivo) }}</textarea>
                        <div class="quill-editor" data-target="ta-motivo_caso" data-height="80" style="min-height:80px"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notas iniciales</label>
                        <textarea name="notas_iniciales" id="ta-notas_iniciales" class="d-none">{{ old('notas_iniciales') }}</textarea>
                        <div class="quill-editor" data-target="ta-notas_iniciales" data-height="80" style="min-height:80px"></div>
                    </div>
                </div>

                {{-- ── Zona Podológica (debajo de motivo del caso) ──── --}}
                <div class="atc-section-title mt-1">
                    <i class="ti ti-map-pin"></i> Zona Podológica Afectada <small class="text-muted fw-normal">(opcional)</small>
                </div>
                @include('panel_especialista.atencion._partials.zonas-podologicas')
            </div>

            <hr class="atc-divider">

            {{-- ── Sección: Consulta ─────────────────────────────────── --}}
            <div class="atc-section-title mt-1">
                <i class="ti ti-notes-medical"></i> Registro de la Consulta
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Observaciones</label>
                    <textarea name="observaciones" id="ta-observaciones" class="d-none">{{ old('observaciones') }}</textarea>
                    <div class="quill-editor" data-target="ta-observaciones" style="min-height:100px"></div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Diagnóstico</label>
                    <textarea name="diagnostico" id="ta-diagnostico" class="d-none">{{ old('diagnostico') }}</textarea>
                    <div class="quill-editor" data-target="ta-diagnostico" style="min-height:100px"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tratamiento</label>
                    <textarea name="tratamiento" id="ta-tratamiento" class="d-none">{{ old('tratamiento') }}</textarea>
                    <div class="quill-editor" data-target="ta-tratamiento" style="min-height:100px"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Indicaciones</label>
                    <textarea name="indicaciones" id="ta-indicaciones" class="d-none">{{ old('indicaciones') }}</textarea>
                    <div class="quill-editor" data-target="ta-indicaciones" style="min-height:100px"></div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Receta</label>
                    <textarea name="receta" id="ta-receta" class="d-none">{{ old('receta') }}</textarea>
                    <div class="quill-editor" data-target="ta-receta" style="min-height:100px"></div>
                </div>
            </div>

            <hr class="atc-divider">

            {{-- ── Sección: Fotos / Adjuntos ────────────────────────── --}}
            <div class="atc-section-title">
                <i class="ti ti-camera"></i> Fotografías de la Consulta <small class="text-muted fw-normal">(opcional)</small>
            </div>

            <div class="atc-fotos-upload" id="fotos-drop-zone">
                <input type="file" id="fotos-input" name="fotos[]" multiple accept="image/*" class="d-none">
                <i class="ti ti-photo-up atc-fotos-icon"></i>
                <p class="mb-1 fw-semibold">Arrastra fotos aquí o <span class="text-primary" style="cursor:pointer" onclick="document.getElementById('fotos-input').click()">selecciona archivos</span></p>
                <p class="text-muted small mb-0">JPG, PNG, WEBP · máx. 5 MB por foto</p>
            </div>

            <div id="fotos-preview" class="atc-fotos-grid mt-3"></div>

            <hr class="atc-divider">

            {{-- ── Botón guardar ────────────────────────────────────── --}}
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('panel.agenda', ['fecha' => is_object($cita->fecha) ? $cita->fecha->toDateString() : $cita->fecha]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2" id="btn-guardar">
                    <i class="ti ti-device-floppy"></i>
                    Guardar consulta y marcar como atendida
                </button>
            </div>

        </form>

    </div>
</div>

{{-- Errores de validación --}}
@if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@push('styles')
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<style>
    .ql-container { border-radius: 0 0 6px 6px; font-family: inherit; font-size: .875rem; }
    .ql-toolbar { border-radius: 6px 6px 0 0; background: #f8fafc; border-color: #dee2e6; }
    .ql-container.ql-snow { border-color: #dee2e6; }
    .ql-editor { min-height: inherit; }
    .ql-editor.ql-blank::before { color: #adb5bd; font-style: normal; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    const TOOLBAR = [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'header': [2, 3, false] }],
        ['clean']
    ];

    document.querySelectorAll('.quill-editor').forEach(function (el) {
        const taId    = el.dataset.target;
        const ta      = document.getElementById(taId);
        const initial = ta ? ta.value.trim() : '';

        const q = new Quill(el, {
            theme:       'snow',
            modules:     { toolbar: TOOLBAR },
            placeholder: ta ? (ta.placeholder || '') : '',
        });

        // Cargar contenido inicial si existe
        if (initial) {
            // Si empieza con <, es HTML; si no, es texto plano
            if (initial.startsWith('<')) {
                q.root.innerHTML = initial;
            } else {
                q.setText(initial);
            }
        }

        // Sincronizar al escribir
        q.on('text-change', function () {
            if (ta) ta.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
        });
    });

    // Sincronizar todos antes de enviar el form
    document.getElementById('form-consulta')?.addEventListener('submit', function () {
        document.querySelectorAll('.quill-editor').forEach(function (el) {
            const ta = document.getElementById(el.dataset.target);
            const q  = Quill.find(el);
            if (ta && q) {
                ta.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
            }
        });
    });
})();
</script>
@endpush
