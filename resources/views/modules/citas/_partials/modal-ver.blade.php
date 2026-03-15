<div class="modal fade" id="modal-ver-cita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-ver-cita-dialog">
        <div class="modal-content border-0 shadow" style="border-radius:14px;overflow:hidden;">

            {{-- Header con estatus --}}
            <div class="cita-ver-header">
                <div class="d-flex align-items-center gap-2">
                    <span id="ver-estatus-badge" class="badge-estatus"></span>
                    <span class="cita-ver-title">Detalle de Cita</span>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>

            {{-- Tabs nav --}}
            <ul class="nav cita-ver-tabs" id="citaVerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="cita-ver-tab active" id="tab-detalle-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-ver-detalle"
                        type="button" role="tab" aria-selected="true">
                        <i class="ti ti-info-circle me-1"></i>Detalle
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="cita-ver-tab" id="tab-actividad-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-ver-actividad"
                        type="button" role="tab" aria-selected="false">
                        <i class="ti ti-history me-1"></i>Actividad
                        <span class="cita-ver-act-badge" id="ver-act-badge" style="display:none"></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                {{-- ── Tab Detalle ─────────────────────────────────────── --}}
                <div class="tab-pane fade show active" id="tab-ver-detalle" role="tabpanel">
                    <div class="modal-body p-0">

                        {{-- Hora + Fecha --}}
                        <div class="cita-ver-block cita-ver-block--top">
                            <div class="cita-ver-row">
                                <span class="cita-ver-label">Hora</span>
                                <span class="cita-ver-value fw-semibold" id="ver-hora">—</span>
                                <span class="cita-ver-mins" id="ver-duracion"></span>
                            </div>
                            <div class="cita-ver-row">
                                <span class="cita-ver-label">Fecha</span>
                                <span class="cita-ver-value" id="ver-fecha">—</span>
                            </div>
                        </div>

                        <div class="cita-ver-divider"></div>

                        {{-- Paciente --}}
                        <div class="cita-ver-block">
                            <div class="cita-ver-row align-items-start">
                                <span class="cita-ver-label">Cliente</span>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a id="ver-paciente-link" href="#" class="cita-ver-patient-link" target="_blank">
                                        <span id="ver-paciente">—</span>
                                        <i class="ti ti-chevron-right" style="font-size:.7rem;"></i>
                                    </a>
                                    <a id="ver-whatsapp" href="#" target="_blank" class="cita-ver-icon-btn cita-ver-icon-btn--wa" title="WhatsApp">
                                        <i class="ti ti-brand-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="cita-ver-divider"></div>

                        {{-- Servicio --}}
                        <div class="cita-ver-block" id="ver-servicio-block">
                            <div class="cita-ver-row">
                                <span class="cita-ver-label">Servicio</span>
                                <div>
                                    <div class="cita-ver-servicio-cat" id="ver-servicio-cat"></div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cita-ver-value" id="ver-servicio">—</span>
                                        <span class="cita-ver-precio" id="ver-precio"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2" id="ver-cobrar-wrap" style="display:none;">
                                <button type="button" class="cita-cobrar-btn w-100" id="btn-cobrar">
                                    <i class="ti ti-cash me-1"></i>
                                    Cobrar <span id="ver-cobrar-monto"></span>
                                </button>
                            </div>
                        </div>

                        <div class="cita-ver-divider"></div>

                        {{-- Especialista + Sucursal --}}
                        <div class="cita-ver-block">
                            <div class="cita-ver-row">
                                <span class="cita-ver-label">Especialista</span>
                                <span class="cita-ver-value" id="ver-especialista">—</span>
                            </div>
                            <div class="cita-ver-row">
                                <span class="cita-ver-label">Sucursal</span>
                                <span class="cita-ver-value" id="ver-sucursal">—</span>
                            </div>
                            <div class="cita-ver-row">
                                <span class="cita-ver-label">Origen</span>
                                <span class="cita-ver-value" id="ver-origen">—</span>
                            </div>
                        </div>

                        {{-- Notas --}}
                        <div id="ver-notas-block" style="display:none;">
                            <div class="cita-ver-divider"></div>
                            <div class="cita-ver-block">
                                <div class="cita-ver-label mb-1">Notas</div>
                                <div class="cita-ver-notas" id="ver-motivo-texto">—</div>
                            </div>
                        </div>

                        {{-- Estatus --}}
                        <div class="cita-ver-divider"></div>
                        <div class="cita-ver-block pb-2">
                            <form id="form-estatus" data-cita-id="">
                                <div class="d-flex gap-2 align-items-center">
                                    <select id="select-estatus" class="form-select form-select-sm" style="max-width:200px;">
                                        <option value="pendiente">En espera de confirmación</option>
                                        <option value="confirmada">Confirmada</option>
                                        <option value="atendida">Atendida</option>
                                        <option value="cancelada">Cancelada</option>
                                        <option value="no_asistio">No asistió</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary px-3">
                                        <i class="ti ti-check me-1"></i>Guardar
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- ── Tab Actividad ────────────────────────────────────── --}}
                <div class="tab-pane fade" id="tab-ver-actividad" role="tabpanel">
                    <div class="cita-ver-actividad-wrap">

                        <div class="la-header" style="margin-bottom:16px;">
                            <i class="ti ti-history la-header-icon"></i>
                            <span class="la-header-title">Historial de Actividad</span>
                            <span class="la-total-badge" id="la-citas-modal-badge">—</span>
                        </div>

                        <div class="la-feed" id="la-citas-modal-feed"></div>

                        <div class="la-sentinel" id="la-citas-modal-sentinel">
                            <span class="la-spinner" id="la-citas-modal-spinner" style="display:none"></span>
                        </div>

                        <div class="la-empty" id="la-citas-modal-empty" style="display:none">
                            <i class="ti ti-clipboard-off"></i>
                            <p>Sin actividades registradas aún.</p>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="cita-ver-footer">
                <button type="button" class="cita-ver-footer-btn cita-ver-footer-btn--close" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="cita-ver-footer-btn cita-ver-footer-btn--delete" id="btn-eliminar-cita" data-cita-id="">
                    <i class="ti ti-trash me-1"></i>Eliminar
                </button>
                <button type="button" class="cita-ver-footer-btn cita-ver-footer-btn--edit" id="btn-editar-cita" data-cita-id="">
                    <i class="ti ti-pencil me-1"></i>Editar
                </button>
            </div>

        </div>
    </div>
</div>
