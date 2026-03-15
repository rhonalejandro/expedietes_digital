{{-- Formulario oculto para el soft delete (action se inyecta desde JS) --}}
<form id="form-delete-paciente" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<x-ui.modal-confirm
    id="modal-delete-paciente"
    title="¿Eliminar paciente?"
    message="El registro se moverá a la papelera (soft delete). Puede restaurarse desde la base de datos."
    confirm-text="Sí, eliminar"
    confirm-variant="danger"
/>
