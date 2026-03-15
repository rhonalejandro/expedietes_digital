<div class="modal fade" id="modal-crear-cita" tabindex="-1" aria-labelledby="modal-crear-cita-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">

            <div class="cita-modal-header d-flex align-items-center justify-content-between">
                <h6 class="modal-title" id="modal-crear-cita-label">
                    <i class="ti ti-calendar-plus me-2 text-primary"></i>Nueva Cita
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="form-crear-cita" novalidate>
                <div class="modal-body p-4">
                    <div class="row g-3">

                        {{-- ── PASO 1: Especialista + Sucursal ──────────────────── --}}
                        <div class="col-md-6">
                            <label class="cita-form-label">Especialista <span class="text-danger">*</span></label>
                            <select name="especialista_id" id="cc-especialista" class="form-select form-select-sm" required>
                                <option value="">Seleccionar...</option>
                                @foreach($especialistas as $esp)
                                    <option value="{{ $esp->id }}">{{ $esp->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="cita-form-label">Sucursal <span class="text-danger">*</span></label>
                            <select name="sucursal_id" class="form-select form-select-sm" required>
                                <option value="">Seleccionar...</option>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ── PASO 2 + 3: Calendario + Slots lado a lado ─────────── --}}
                        <div class="col-12" id="cc-bloque-calendario" style="display:none">
                            <div class="cc-cal-slots-wrap">

                                {{-- Columna izquierda: mini calendario --}}
                                <div class="cc-cal-col">
                                    <label class="cita-form-label mb-2">
                                        <i class="ti ti-calendar-event me-1 text-primary"></i>
                                        Selecciona una fecha
                                    </label>
                                    <div id="cc-calendario" class="cc-cal-wrap"></div>
                                    <input type="hidden" name="fecha" id="cc-fecha-hidden" required>
                                </div>

                                {{-- Columna derecha: slots de hora --}}
                                <div class="cc-slots-col" id="cc-bloque-slots" style="display:none">
                                    <label class="cita-form-label mb-2">
                                        <i class="ti ti-clock me-1 text-primary"></i>
                                        Horas disponibles
                                        <span id="cc-fecha-legible" class="text-primary fw-semibold d-block" style="font-size:.78rem;margin-top:2px"></span>
                                    </label>
                                    <div id="cc-slots" class="cc-slots-grid"></div>
                                    <input type="hidden" name="hora_inicio" id="cc-hora-inicio-hidden" required>
                                    <input type="hidden" name="hora_fin"    id="cc-hora-fin-hidden"    required>
                                </div>

                            </div>
                        </div>

                        {{-- ── Paciente: autocomplete server-side ───────────────── --}}
                        <div class="col-md-6" id="cc-bloque-paciente" style="display:none">
                            <label class="cita-form-label">Paciente registrado</label>
                            <div class="cc-autocomplete" id="cc-pac-wrap">
                                <input type="text" id="cc-pac-input" class="form-control form-control-sm"
                                       placeholder="Nombre, teléfono o email..." autocomplete="off">
                                <div class="cc-autocomplete-dropdown" id="cc-pac-dropdown"></div>
                            </div>
                            <input type="hidden" name="paciente_id" id="cc-pac-id">
                            <small class="text-muted" style="font-size:.71rem;">
                                Si no tiene ficha, deja vacío y usa los campos de lead.
                            </small>
                        </div>

                        <div class="col-md-6" id="cc-bloque-servicio" style="display:none">
                            <label class="cita-form-label">Servicio</label>
                            <select name="servicio_id" class="form-select form-select-sm">
                                <option value="">Sin especificar</option>
                                @foreach($servicios as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" id="cc-bloque-lead1" style="display:none">
                            <label class="cita-form-label">Nombre lead</label>
                            <input type="text" name="nombre_lead" class="form-control form-control-sm"
                                   placeholder="Nombre si no tiene ficha">
                        </div>

                        <div class="col-md-6" id="cc-bloque-lead2" style="display:none">
                            <label class="cita-form-label">Teléfono lead</label>
                            <input type="text" name="telefono_lead" class="form-control form-control-sm"
                                   placeholder="+507 6000-0000">
                        </div>

                        <div class="col-md-6" id="cc-bloque-estatus" style="display:none">
                            <label class="cita-form-label">Estatus <span class="text-danger">*</span></label>
                            <select name="estatus" class="form-select form-select-sm" required>
                                <option value="pendiente" selected>Pendiente</option>
                                <option value="confirmada">Confirmada</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="cc-bloque-origen" style="display:none">
                            <label class="cita-form-label">Origen</label>
                            <select name="origen" class="form-select form-select-sm">
                                <option value="web">Web</option>
                                <option value="telefono">Teléfono</option>
                                <option value="chatwoot">Chatwoot</option>
                                <option value="mobile">Mobile</option>
                            </select>
                        </div>

                        <div class="col-12" id="cc-bloque-motivo" style="display:none">
                            <label class="cita-form-label">Motivo de consulta</label>
                            <textarea name="motivo" class="form-control form-control-sm" rows="2"
                                      placeholder="Motivo de la cita..."></textarea>
                        </div>

                        <div class="col-12" id="cc-bloque-obs" style="display:none">
                            <label class="cita-form-label">Observaciones internas</label>
                            <textarea name="observaciones" class="form-control form-control-sm" rows="2"
                                      placeholder="Notas internas..."></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="cc-btn-guardar" style="display:none">
                        <i class="ti ti-check me-1"></i>Guardar Cita
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
