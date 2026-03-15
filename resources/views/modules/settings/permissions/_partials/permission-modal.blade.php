{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Partial: permission-modal.blade.php (< 80 líneas)
Propósito: Modal para crear/editar permisos (se usa con JS)
--}}

@push('scripts')
<script id="permission-modal-template" type="text/template">
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="permissionForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-shield me-2"></i>
                        <span id="modalTitle">Editar Permiso</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Módulo</label>
                            <select name="modulo" class="form-select" required>
                                <option value="">Seleccionar</option>
                                @foreach($modulos as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Código</label>
                            <input type="text" name="codigo" class="form-control" required 
                                   placeholder="ej: view, create, edit">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="general">General (CRUD)</option>
                                <option value="granular">Granular (Especial)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="estado" checked>
                                <label class="form-check-label">Activo</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</script>
@endpush
