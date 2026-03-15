@props([
    'id' => 'modal-confirm-default',
    'title' => '¿Estás seguro?',
    'message' => '',
    'confirmText' => 'Sí, confirmar',
    'cancelText' => 'Cancelar',
    'confirmVariant' => 'danger',
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="h-80 w-80 d-flex-center b-r-50 m-auto bg-primary bg-opacity-10">
                        <i class="ti ti-help f-s-40 text-primary"></i>
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
                        {{ $cancelText }}
                    </button>
                    
                    <button 
                        type="button" 
                        class="btn btn-{{ $confirmVariant }} px-4"
                        data-bs-dismiss="modal"
                        id="{{ $id }}-confirm"
                    >
                        {{ $confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Función global para mostrar confirmación
window.showConfirm = function(title, message, callback) {
    const modalId = '{{ $id }}';
    const modalEl = document.getElementById(modalId);
    
    if (!modalEl) {
        console.error('Modal de confirmación no encontrado');
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
