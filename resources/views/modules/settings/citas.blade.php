<div class="tab-pane fade" id="citas" role="tabpanel">
    <div class="card settings-card">
        <div class="card-header bg-transparent border-0">
            <h5 class="mb-0">
                <i class="ti ti-calendar-event me-2"></i>Configuración de Citas
            </h5>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('settings.citas.update') }}">
                @csrf

                {{-- ── Modo de agenda ──────────────────────────────── --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Modo de agenda</label>
                    <p class="text-muted small mb-3">
                        Define si el sistema permite o no solapar citas del mismo especialista.
                    </p>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="modo_agenda"
                                   id="modo-estricto" value="estricto"
                                   {{ ($empresa?->modo_agenda ?? 'estricto') === 'estricto' ? 'checked' : '' }}>
                            <label class="card h-100 cursor-pointer border-2 p-3" for="modo-estricto"
                                   style="cursor:pointer">
                                <div class="d-flex align-items-start gap-3">
                                    <span style="font-size:2rem">🔒</span>
                                    <div>
                                        <div class="fw-bold mb-1">Estricto</div>
                                        <div class="text-muted small">
                                            Al crear una cita se muestran <strong>solo los horarios disponibles</strong>
                                            del especialista. El sistema bloquea la creación si hay solapamiento
                                            con otra cita.
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="modo_agenda"
                                   id="modo-sobrecarga" value="sobrecarga"
                                   {{ ($empresa?->modo_agenda ?? 'estricto') === 'sobrecarga' ? 'checked' : '' }}>
                            <label class="card h-100 cursor-pointer border-2 p-3" for="modo-sobrecarga"
                                   style="cursor:pointer">
                                <div class="d-flex align-items-start gap-3">
                                    <span style="font-size:2rem">⚡</span>
                                    <div>
                                        <div class="fw-bold mb-1">Sobrecarga</div>
                                        <div class="text-muted small">
                                            Se ingresan la hora de inicio y fin <strong>libremente</strong>,
                                            sin restricciones. Permite montar citas sobre el mismo horario
                                            del especialista (doble agenda).
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                    </div>
                </div>

                <hr class="my-4">

                {{-- ── Colores de estados ──────────────────────────── --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Color por estado de cita</label>
                    <p class="text-muted small mb-3">
                        El color se aplica al borde izquierdo de cada cita en el calendario.
                    </p>

                    @php
                        $colores = $empresa?->colores_estatus ?? \App\Models\Empresa::COLORES_DEFAULT;
                        $estados = [
                            'pendiente'   => 'Pendiente',
                            'confirmada'  => 'Confirmada',
                            'en_consulta' => 'En Consulta',
                            'atendida'    => 'Atendida',
                            'cancelada'   => 'Cancelada',
                            'no_asistio'  => 'No asistió',
                        ];
                    @endphp

                    <div class="row g-3">
                        @foreach($estados as $key => $label)
                        <div class="col-md-4 col-sm-6">
                            <div class="color-estatus-card d-flex align-items-center gap-3 p-3 rounded-3 border"
                                 style="background:#fafbfc">
                                <input type="color"
                                       name="colores_estatus[{{ $key }}]"
                                       value="{{ $colores[$key] ?? '#64748b' }}"
                                       class="estatus-color-input"
                                       data-preview="{{ $key }}"
                                       title="Color para {{ $label }}">
                                <div>
                                    <div class="fw-semibold" style="font-size:.85rem">{{ $label }}</div>
                                    <code class="estatus-color-code text-muted" style="font-size:.72rem"
                                          id="code-{{ $key }}">{{ $colores[$key] ?? '#64748b' }}</code>
                                </div>
                                {{-- Vista previa del borde izquierdo --}}
                                <div class="ms-auto estatus-border-preview rounded"
                                     id="preview-{{ $key }}"
                                     style="width:6px;height:40px;background:{{ $colores[$key] ?? '#64748b' }};border-radius:4px;flex-shrink:0"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-device-floppy me-1"></i>Guardar configuración
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<style>
#modo-estricto:checked  + label { border-color: #667eea !important; background: rgba(102,126,234,.06); }
#modo-sobrecarga:checked + label { border-color: #667eea !important; background: rgba(102,126,234,.06); }

.estatus-color-input {
    width: 44px;
    height: 44px;
    padding: 2px;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    cursor: pointer;
    flex-shrink: 0;
    background: none;
}
.color-estatus-card { transition: border-color .15s; }
.color-estatus-card:focus-within { border-color: #667eea !important; }
</style>

<script>
document.querySelectorAll('.estatus-color-input').forEach(function(input) {
    input.addEventListener('input', function() {
        var key = this.dataset.preview;
        var color = this.value;
        var preview = document.getElementById('preview-' + key);
        var code    = document.getElementById('code-' + key);
        if (preview) preview.style.background = color;
        if (code)    code.textContent = color;
    });
});
</script>
