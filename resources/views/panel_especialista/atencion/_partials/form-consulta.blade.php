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
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Motivo del caso <span class="text-danger">*</span></label>
                        <input type="text" name="motivo_caso" class="form-control form-control-sm"
                               placeholder="Ej: Dolor plantar, hallux valgus..."
                               value="{{ old('motivo_caso', $cita->motivo) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notas iniciales</label>
                        <textarea name="notas_iniciales" class="form-control form-control-sm" rows="2"
                                  placeholder="Antecedentes, observaciones iniciales del caso...">{{ old('notas_iniciales') }}</textarea>
                    </div>
                </div>
            </div>

            <hr class="atc-divider">

            {{-- ── Sección: Consulta ─────────────────────────────────── --}}
            <div class="atc-section-title mt-1">
                <i class="ti ti-notes-medical"></i> Registro de la Consulta
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Observaciones</label>
                    <textarea name="observaciones" class="form-control form-control-sm" rows="3"
                              placeholder="Hallazgos clínicos, síntomas observados...">{{ old('observaciones') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Diagnóstico</label>
                    <textarea name="diagnostico" class="form-control form-control-sm" rows="3"
                              placeholder="Diagnóstico clínico...">{{ old('diagnostico') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tratamiento</label>
                    <textarea name="tratamiento" class="form-control form-control-sm" rows="3"
                              placeholder="Procedimientos realizados, terapias aplicadas...">{{ old('tratamiento') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Indicaciones</label>
                    <textarea name="indicaciones" class="form-control form-control-sm" rows="3"
                              placeholder="Instrucciones para el paciente, cuidados en casa...">{{ old('indicaciones') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Receta</label>
                    <textarea name="receta" class="form-control form-control-sm" rows="3"
                              placeholder="Medicamentos, dosis, frecuencia...">{{ old('receta') }}</textarea>
                </div>
            </div>

            <hr class="atc-divider">

            {{-- ── Sección: Zona Podológica ─────────────────────────── --}}
            <div class="atc-section-title">
                <i class="ti ti-map-pin"></i> Zona Podológica Afectada <small class="text-muted fw-normal">(opcional)</small>
            </div>

            @include('panel_especialista.atencion._partials.zonas-podologicas')

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
