@php
    $sucursales = $sucursales ?? collect();
    $total = $total ?? 0;
    $activas = $activas ?? 0;
@endphp

<div class="tab-pane fade" id="sucursales" role="tabpanel">
    <div class="card settings-card">
        <div class="card-header bg-transparent border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-map-pin me-2"></i>Sucursales
                    <span class="badge bg-primary ms-2">{{ $total }}</span>
                </h5>
                <x-ui.button 
                    variant="primary" 
                    icon="ti ti-plus"
                    data-bs-toggle="modal"
                    data-bs-target="#addSucursalModal"
                >
                    Agregar Sucursal
                </x-ui.button>
            </div>
        </div>
        <div class="card-body">
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="text-muted small">Total Sucursales</div>
                        <div class="h3 mb-0 text-primary">{{ $total }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="text-muted small">Sucursales Activas</div>
                        <div class="h3 mb-0 text-success">{{ $activas }}</div>
                    </div>
                </div>
            </div>

            <!-- Lista -->
            <x-settings.tabs.sucursales.list 
                :sucursales="$sucursales"
                :total="$total"
                :activas="$activas"
            />
        </div>
    </div>

    <!-- Modals -->
    <x-settings.tabs.sucursales.modal-create />
    <x-settings.tabs.sucursales.modal-edit />
    
    <!-- Modales de Alerta y Confirmación -->
    <x-ui.modal-alert id="modal-alert-info" type="info" title="Información" />
    <x-ui.modal-alert id="modal-alert-success" type="success" title="Éxito" />
    <x-ui.modal-alert id="modal-alert-warning" type="warning" title="Advertencia" />
    <x-ui.modal-alert id="modal-alert-error" type="error" title="Error" />
    <x-ui.modal-confirm 
        id="modal-confirm-default"
        title="¿Estás seguro?" 
        confirm-text="Sí, confirmar"
        confirm-variant="danger"
    />
</div>

@push('scripts')
<script>
// Editar sucursal
function editSucursal(sucursal) {
    console.log('Editando sucursal:', sucursal);

    // Usar setTimeout para asegurar que el DOM esté listo
    setTimeout(function() {
        // Verificar que el modal existe
        const modalEl = document.getElementById('editSucursalModal');
        if (!modalEl) {
            console.error('Modal no encontrado');
            console.log('Elementos en el DOM:', document.querySelectorAll('*').length);
            alert('Error: Modal de edición no encontrado. Recarga la página.');
            return;
        }

        // Verificar y llenar campos uno por uno
        const campos = [
            { id: 'edit_sucursal_id', value: sucursal.id, required: true },
            { id: 'edit_nombre', value: sucursal.nombre, required: true },
            { id: 'edit_direccion', value: sucursal.direccion, required: true },
            { id: 'edit_telefono', value: sucursal.telefono || sucursal.contacto || '', required: false },
            { id: 'edit_email', value: sucursal.email || '', required: false },
            { id: 'edit_encargado',     value: sucursal.encargado     || '', required: false },
            { id: 'edit_hora_apertura', value: (sucursal.hora_apertura || '09:00:00').slice(0,5), required: false },
            { id: 'edit_hora_cierre',   value: (sucursal.hora_cierre   || '18:00:00').slice(0,5), required: false },
            { id: 'edit_estado', value: sucursal.estado, required: false, isCheckbox: true }
        ];

        let error = false;
        campos.forEach(campo => {
            const elemento = document.getElementById(campo.id);
            if (!elemento) {
                console.error('Campo no encontrado:', campo.id);
                error = true;
            } else if (campo.isCheckbox) {
                elemento.checked = campo.value;
            } else {
                elemento.value = campo.value;
            }
        });

        // Cargar imagen existente si hay
        const previewEl = document.getElementById('editImagenPreview');
        if (previewEl && sucursal.imagen) {
            previewEl.innerHTML = '<img src="/storage/' + sucursal.imagen + '" alt="' + sucursal.nombre + '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">';
            previewEl.classList.remove('bg-light');
        } else if (previewEl) {
            previewEl.innerHTML = '<i class="ti ti-camera f-s-32 text-muted"></i>';
            previewEl.classList.add('bg-light');
        }

        if (error) {
            console.error('Hay campos faltantes en el modal. Revisa la vista del modal.');
            alert('Error: El modal tiene campos faltantes. Contacta al administrador.');
            return;
        }

        // Actualizar acción del formulario
        document.getElementById('editSucursalForm').action = '/settings/sucursal/' + sucursal.id;

        // Mostrar modal
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        console.log('Modal mostrado exitosamente');
    }, 100);
}

// Toggle estado sucursal con confirmación
function toggleSucursal(id) {
    showConfirm(
        'Cambiar Estado',
        '¿Estás seguro de que deseas cambiar el estado de esta sucursal?',
        function() {
            window.location.href = '/settings/sucursal/' + id + '/toggle';
        }
    );
}

// Eliminar sucursal con confirmación
function deleteSucursal(id) {
    showConfirm(
        'Eliminar Sucursal',
        '¿Estás seguro de que deseas eliminar esta sucursal? Esta acción no se puede deshacer.',
        function() {
            // Crear formulario temporal y enviar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/settings/sucursal/' + id;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]').content;
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    );
}

// Probar alerta
console.log('Scripts de sucursales cargados correctamente');
console.log('Funciones disponibles: editSucursal, toggleSucursal, deleteSucursal, showAlert, showConfirm');
</script>
@endpush
