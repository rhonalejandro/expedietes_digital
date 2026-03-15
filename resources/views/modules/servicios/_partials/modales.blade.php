{{-- ── Modal Crear ─────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="modal-crear-servicio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 shadow-sm">
            <div class="srv-modal-header d-flex align-items-center justify-content-between">
                <h6 class="modal-title">
                    <i class="ti ti-medical-cross me-2 text-primary"></i>Nuevo Servicio
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-crear-servicio" novalidate>
                <div class="modal-body p-4">
                    @include('modules.servicios._partials.form-fields')
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ti ti-check me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal Editar ────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="modal-editar-servicio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 shadow-sm">
            <div class="srv-modal-header d-flex align-items-center justify-content-between">
                <h6 class="modal-title">
                    <i class="ti ti-edit me-2 text-primary"></i>Editar Servicio
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-editar-servicio" novalidate data-servicio-id="">
                <div class="modal-body p-4">
                    @include('modules.servicios._partials.form-fields')
                    <div class="mt-3 pt-3 border-top">
                        <label class="srv-form-label d-block mb-2">Estado</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="estado" id="edit-estado" value="1">
                            <label class="form-check-label" for="edit-estado">Servicio activo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ti ti-check me-1"></i>Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
