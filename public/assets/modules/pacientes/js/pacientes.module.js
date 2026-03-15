/**
 * pacientes.module.js
 *
 * Funcionalidades del módulo de pacientes:
 *   1. Toggle de estado activo/inactivo vía AJAX (sin recargar tabla completa).
 *   2. Confirmación de eliminación (soft delete) usando window.showConfirm.
 *
 * Dependencias:
 *   - Bootstrap (para el modal via window.showConfirm definido en x-ui.modal-confirm)
 *   - meta[name="csrf-token"] presente en el <head> del master layout
 */

document.addEventListener('DOMContentLoaded', function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Toggle estado (activo / inactivo) ─────────────────────────────────────

    document.querySelectorAll('.btn-toggle-estado').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id     = this.dataset.id;
            const activo = this.dataset.estado === '1';
            const accion = activo ? 'desactivar' : 'activar';

            window.showConfirm(
                (activo ? '¿Desactivar' : '¿Activar') + ' paciente?',
                'El paciente quedará marcado como ' + (activo ? 'inactivo' : 'activo') + '.',
                function () {
                    fetch('/pacientes/' + id + '/toggle', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept':       'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (data.success) {
                            // Recarga la página para reflejar el nuevo estado
                            window.location.reload();
                        }
                    })
                    .catch(function () {
                        alert('Error al cambiar el estado. Por favor, intente nuevamente.');
                    });
                }
            );
        });
    });

    // ── Eliminar paciente (soft delete) ───────────────────────────────────────

    document.querySelectorAll('.btn-delete-paciente').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id     = this.dataset.id;
            const nombre = this.dataset.nombre;

            window.showConfirm(
                '¿Eliminar paciente?',
                'Se eliminará el registro de "' + nombre + '". Esta acción es reversible desde la base de datos (soft delete).',
                function () {
                    const form  = document.getElementById('form-delete-paciente');
                    form.action = '/pacientes/' + id;
                    form.submit();
                }
            );
        });
    });

});
