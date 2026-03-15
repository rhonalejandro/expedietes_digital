@props([
    'id' => 'modal-alert-default',
    'type' => 'info',
    'title' => 'Información',
    'message' => '',
])

@php
$icons = [
    'info' => 'ti ti-info-circle text-info',
    'success' => 'ti  text-success',
    'warning' => 'ti ti-alert-triangle text-warning',
    'error' => 'ti ti-x text-danger',
    'question' => 'ti ti-help text-primary',
];

$bgColors = [
    'info' => 'bg-info bg-opacity-10',
    'success' => 'bg-success bg-opacity-10',
    'warning' => 'bg-warning bg-opacity-10',
    'error' => 'bg-danger bg-opacity-10',
    'question' => 'bg-primary bg-opacity-10',
];

$iconClass = $icons[$type] ?? $icons['info'];
$bgClass = $bgColors[$type] ?? $bgColors['info'];
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="h-80 w-80 d-flex-center b-r-50 m-auto {{ $bgClass }}">
                        <i class="{{ $iconClass }} f-s-40"></i>
                    </div>
                </div>
                
                <h5 class="mb-2" id="{{ $id }}-title">{{ $title }}</h5>
                <p class="text-muted mb-4" id="{{ $id }}-message">{{ $message }}</p>
                
                <div class="d-flex gap-2 justify-content-center">
                    <button 
                        type="button" 
                        class="btn btn-light px-4"
                        data-bs-dismiss="modal"
                        id="{{ $id }}-cancel"
                    >
                        Cancelar
                    </button>
                    
                    <button 
                        type="button" 
                        class="btn {{ $type === 'success' ? 'btn-success' : '' }} {{ $type === 'warning' ? 'btn-warning' : '' }} {{ $type === 'error' ? 'btn-danger' : '' }} {{ $type === 'info' ? 'btn-primary' : '' }} px-4"
                        data-bs-dismiss="modal"
                        id="{{ $id }}-confirm"
                    >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Funciones globales para mostrar modales
window.showAlert = function(title, message, type = 'info') {
    const modalId = 'modal-alert-' + type;
    const modalEl = document.getElementById(modalId);
    
    if (!modalEl) {
        console.error('Modal no encontrado:', modalId);
        alert(title + ': ' + message);
        return;
    }
    
    // Actualizar título y mensaje
    document.getElementById(modalId + '-title').textContent = title || 'Información';
    document.getElementById(modalId + '-message').textContent = message || '';
    
    // Mostrar modal
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
};

window.showConfirm = function(title, message, callback, type = 'question') {
    const modalId = 'modal-alert-' + type;
    const modalEl = document.getElementById(modalId);
    
    if (!modalEl) {
        console.error('Modal no encontrado:', modalId);
        if (confirm(title + ': ' + message)) {
            callback();
        }
        return;
    }
    
    // Actualizar título y mensaje
    document.getElementById(modalId + '-title').textContent = title || '¿Estás seguro?';
    document.getElementById(modalId + '-message').textContent = message || '';
    
    // Remover listeners anteriores
    const confirmBtn = document.getElementById(modalId + '-confirm');
    const cancelBtn = document.getElementById(modalId + '-cancel');
    
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Agregar listener para confirmar
    newConfirmBtn.addEventListener('click', function() {
        if (typeof callback === 'function') {
            callback();
        }
    });
    
    // Mostrar modal
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
};
</script>
@endpush
