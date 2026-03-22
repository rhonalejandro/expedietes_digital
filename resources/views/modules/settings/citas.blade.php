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
</style>
