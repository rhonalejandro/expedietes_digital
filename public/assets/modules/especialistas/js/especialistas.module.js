/**
 * especialistas.module.js
 * Toggle de estado y confirmación de eliminación.
 */
document.addEventListener('DOMContentLoaded', function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Toggle estado ─────────────────────────────────────────────────────────

    document.querySelectorAll('.btn-toggle-esp').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id     = this.dataset.id;
            const activo = this.dataset.estado === '1';

            window.showConfirm(
                (activo ? '¿Desactivar' : '¿Activar') + ' especialista?',
                'El especialista quedará marcado como ' + (activo ? 'inactivo' : 'activo') + '.',
                function () {
                    fetch('/especialistas/' + id + '/toggle', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept':       'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(r => r.json())
                    .then(data => { if (data.success) window.location.reload(); })
                    .catch(() => alert('Error al cambiar el estado.'));
                }
            );
        });
    });

    // ── Eliminar ──────────────────────────────────────────────────────────────

    document.querySelectorAll('.btn-delete-esp').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id     = this.dataset.id;
            const nombre = this.dataset.nombre;

            window.showConfirm(
                '¿Eliminar especialista?',
                'Se eliminará el registro de "' + nombre + '".',
                function () {
                    const form  = document.getElementById('form-delete-esp');
                    form.action = '/especialistas/' + id;
                    form.submit();
                }
            );
        });
    });

});
