/**
 * ServiciosModule — servicios.module.js
 * CRUD completo vía modales AJAX. Sin dependencias externas.
 */
const ServiciosModule = (function () {

    let CONFIG = {};

    function init(cfg) {
        CONFIG = cfg;
        _bindCrear();
        _bindEditar();
        _bindToggle();
        _bindEliminar();
    }

    // ── Crear ─────────────────────────────────────────────────────────────────

    function _bindCrear() {
        const form = document.getElementById('form-crear-servicio');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            _submitForm(form, CONFIG.storeUrl, 'POST', function () {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-crear-servicio')).hide();
                form.reset();
                location.reload();
            });
        });
    }

    // ── Editar ────────────────────────────────────────────────────────────────

    function _bindEditar() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-action="editar"]');
            if (!btn) return;

            const id = btn.dataset.id;
            fetch(`${CONFIG.baseUrl}/${id}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CONFIG.csrf }
            })
            .then(r => r.json())
            .then(srv => {
                const modal = document.getElementById('modal-editar-servicio');
                modal.querySelector('[name="nombre"]').value      = srv.nombre;
                modal.querySelector('[name="descripcion"]').value = srv.descripcion || '';
                modal.querySelector('[name="precio"]').value      = srv.precio;
                modal.querySelector('[name="estado"]').checked    = srv.estado;

                const form = modal.querySelector('#form-editar-servicio');
                form.dataset.servicioId = id;

                bootstrap.Modal.getOrCreateInstance(modal).show();
            })
            .catch(() => _toast('No se pudo cargar el servicio.', 'danger'));
        });

        const formEditar = document.getElementById('form-editar-servicio');
        if (!formEditar) return;

        formEditar.addEventListener('submit', function (e) {
            e.preventDefault();
            const id  = this.dataset.servicioId;
            const url = `${CONFIG.baseUrl}/${id}`;

            _submitForm(this, url, 'PUT', function () {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-editar-servicio')).hide();
                location.reload();
            });
        });
    }

    // ── Toggle estado ─────────────────────────────────────────────────────────

    function _bindToggle() {
        document.addEventListener('change', function (e) {
            const toggle = e.target.closest('.srv-toggle');
            if (!toggle) return;

            const id  = toggle.dataset.id;
            fetch(`${CONFIG.baseUrl}/${id}/toggle`, {
                method:  'PATCH',
                headers: { 'X-CSRF-TOKEN': CONFIG.csrf, 'Accept': 'application/json' },
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) toggle.checked = !toggle.checked;
                _toast(data.mensaje, data.success ? 'success' : 'danger');
            })
            .catch(() => { toggle.checked = !toggle.checked; });
        });
    }

    // ── Eliminar ──────────────────────────────────────────────────────────────

    function _bindEliminar() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-action="eliminar"]');
            if (!btn) return;

            const id     = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            if (!confirm(`¿Eliminar el servicio "${nombre}"? Esta acción no se puede deshacer.`)) return;

            fetch(`${CONFIG.baseUrl}/${id}`, {
                method:  'DELETE',
                headers: { 'X-CSRF-TOKEN': CONFIG.csrf, 'Accept': 'application/json' },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const row = document.querySelector(`[data-row-id="${id}"]`);
                    if (row) row.remove();
                    _toast('Servicio eliminado.', 'success');
                } else {
                    _toast(data.mensaje, 'danger');
                }
            });
        });
    }

    // ── Helper: submit form con _method spoofing ──────────────────────────────

    function _submitForm(form, url, method, onSuccess) {
        _limpiarErrores(form);
        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        const fd = new FormData(form);
        if (method === 'PUT') { fd.append('_method', 'PUT'); }

        fetch(url, {
            method:  method === 'PUT' ? 'POST' : 'POST',
            headers: { 'X-CSRF-TOKEN': CONFIG.csrf, 'Accept': 'application/json' },
            body:    fd,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                _toast(data.mensaje, 'success');
                onSuccess(data);
            } else if (data.errors) {
                _mostrarErrores(data.errors, form);
            } else {
                _toast(data.message || 'Error inesperado.', 'danger');
            }
        })
        .catch(() => _toast('Error de conexión.', 'danger'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // ── Utilidades ────────────────────────────────────────────────────────────

    function _limpiarErrores(form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    }

    function _mostrarErrores(errors, form) {
        for (const [campo, msgs] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${campo}"]`);
            if (input) {
                input.classList.add('is-invalid');
                input.insertAdjacentHTML('afterend', `<div class="invalid-feedback">${msgs[0]}</div>`);
            }
        }
    }

    function _toast(msg, tipo = 'success') {
        const wrap = document.getElementById('toast-container');
        if (!wrap) return;
        const id  = 'toast-' + Date.now();
        const cls = tipo === 'success' ? 'bg-success' : 'bg-danger';
        wrap.insertAdjacentHTML('beforeend', `
            <div id="${id}" class="toast align-items-center text-white ${cls} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${msg}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`);
        const t = new bootstrap.Toast(document.getElementById(id), { delay: 3500 });
        t.show();
        document.getElementById(id).addEventListener('hidden.bs.toast', () => document.getElementById(id)?.remove());
    }

    return { init };
})();
