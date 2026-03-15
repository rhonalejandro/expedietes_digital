<form id="form-delete-esp" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<x-ui.modal-confirm
    id="modal-delete-esp"
    title="¿Eliminar especialista?"
    message="El registro será eliminado del sistema."
    confirm-text="Sí, eliminar"
    confirm-variant="danger"
/>
