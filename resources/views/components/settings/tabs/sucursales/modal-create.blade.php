<div class="modal fade" id="addSucursalModal" tabindex="-1" aria-labelledby="addSucursalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('settings.sucursal.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="modal-header">
                    <h5 class="modal-title" id="addSucursalModalLabel">
                        <i class="ti ti-building me-2"></i>Agregar Nueva Sucursal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Imagen de la sucursal -->
                        <div class="col-12 text-center mb-3">
                            <div class="sucursal-imagen-preview-container mb-2">
                                <div class="sucursal-imagen-preview d-flex-center bg-light" id="createImagenPreview">
                                    <i class="ti ti-camera f-s-32 text-muted"></i>
                                </div>
                            </div>
                            <label class="logo-upload-btn btn-sm" for="createImagenInput">
                                <i class="ti ti-camera me-2"></i>Seleccionar Imagen
                            </label>
                            <input
                                type="file"
                                name="imagen"
                                id="createImagenInput"
                                accept="image/*"
                                style="display: none;"
                                onchange="previewSucursalImagen(event, 'createImagenPreview')"
                            >
                            <p class="text-muted small mt-1 mb-0">PNG, JPG hasta 2MB</p>
                        </div>

                        <x-ui.input
                            name="nombre"
                            label="Nombre de la Sucursal"
                            placeholder="Ej: Sucursal Centro"
                            :required="true"
                        />

                        <div class="col-12">
                            <label for="direccion" class="form-label">Dirección *</label>
                            <textarea
                                name="direccion"
                                id="direccion"
                                class="form-control @error('direccion') is-invalid @enderror"
                                rows="2"
                                placeholder="Dirección completa"
                                required
                            ></textarea>
                            @error('direccion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <x-ui.input
                                name="telefono"
                                label="Teléfono / Contacto"
                                placeholder="+506 0000 0000"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-ui.input
                                name="email"
                                label="Correo Electrónico"
                                type="email"
                                placeholder="sucursal@empresa.com"
                            />
                        </div>

                        <div class="col-12">
                            <x-ui.input
                                name="encargado"
                                label="Encargado"
                                placeholder="Nombre del encargado"
                            />
                        </div>

                        {{-- Horario laboral --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-1">
                                <i class="ti ti-clock me-1 text-primary"></i>Horario laboral
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="flex-grow-1">
                                    <label class="form-label small text-muted mb-1">Apertura</label>
                                    <input type="time" name="hora_apertura" class="form-control form-control-sm" value="09:00" required>
                                </div>
                                <span class="text-muted mt-3">—</span>
                                <div class="flex-grow-1">
                                    <label class="form-label small text-muted mb-1">Cierre</label>
                                    <input type="time" name="hora_cierre" class="form-control form-control-sm" value="18:00" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <x-ui.toggle
                                name="estado"
                                label="Sucursal Activa"
                                :checked="true"
                            />
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <x-ui.button type="button" variant="light" data-bs-dismiss="modal">
                        Cancelar
                    </x-ui.button>
                    
                    <x-ui.button type="submit" variant="primary" icon="ti ti-check">
                        Guardar Sucursal
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewSucursalImagen(event, previewId) {
    const file = event.target.files[0];
    
    if (!file) return;

    // Validar tipo
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        alert('Solo se permiten imágenes JPG, PNG o GIF');
        event.target.value = '';
        return;
    }

    // Validar tamaño (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        alert('La imagen no debe superar los 2MB');
        event.target.value = '';
        return;
    }

    // Mostrar preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewEl = document.getElementById(previewId);
        previewEl.innerHTML = '<img src="' + e.target.result + '" alt="Vista previa" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">';
        previewEl.classList.remove('bg-light');
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
