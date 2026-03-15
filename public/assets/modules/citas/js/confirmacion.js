/* ============================================================
   Confirmación de Citas — confirmacion.js
   Standalone IIFE — does NOT depend on citas.module.js
   ============================================================ */

const ConfirmacionModule = (function () {

    'use strict';

    // ── State ─────────────────────────────────────────────────────────────────
    let _page        = 1;
    let _lastPage    = 1;
    let _query       = '';
    let _filtro      = 'pendientes';   // pendientes | confirmadas | rechazadas
    let _loading     = false;
    let _total       = 0;
    let _observer    = null;
    let _debounceTimer = null;

    // Current cita data for trasladar/edit flows
    let _citaActual  = null;

    // CSRF token
    const _csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ── Month/day labels ──────────────────────────────────────────────────────
    const MESES_ES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                      'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const DIAS_ES  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

    // ── Helpers ───────────────────────────────────────────────────────────────
    function _esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function _labelEstatus(e) {
        return {
            pendiente:  'Pendiente',
            confirmada: 'Confirmada',
            cancelada:  'Cancelada',
            no_asistio: 'No asistió',
            atendida:   'Atendida',
        }[e] || e;
    }

    function _formatFecha(fechaStr) {
        if (!fechaStr) return '';
        const [y, m, d] = fechaStr.split('-');
        const dt = new Date(parseInt(y), parseInt(m) - 1, parseInt(d));
        return DIAS_ES[dt.getDay()] + ' ' + parseInt(d) + ' ' + MESES_ES[dt.getMonth()] + ' ' + y;
    }

    function _formatHora(h) {
        return h ? h.substring(0, 5) : '';
    }

    function _toast(msg, tipo = 'success') {
        const wrap = document.getElementById('toast-container');
        if (!wrap) { alert(msg); return; }
        const id  = 'toast-' + Date.now();
        const cls = tipo === 'success' ? 'bg-success' : 'bg-danger';
        wrap.insertAdjacentHTML('beforeend', `
            <div id="${id}" class="toast align-items-center text-white ${cls} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${_esc(msg)}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`);
        const t = new bootstrap.Toast(document.getElementById(id), { delay: 3500 });
        t.show();
        document.getElementById(id)?.addEventListener('hidden.bs.toast', () => document.getElementById(id)?.remove());
    }

    // ── Load page ─────────────────────────────────────────────────────────────
    function _cargarPagina(page, q, append) {
        if (_loading) return;
        _loading = true;

        const sentinel = document.getElementById('conf-sentinel');
        if (sentinel) sentinel.innerHTML = '<span class="conf-spinner"></span> Cargando...';

        const url = `/citas/confirmacion/lista?page=${page}&q=${encodeURIComponent(q)}&filtro=${encodeURIComponent(_filtro)}`;

        fetch(url, {
            headers: { 'X-CSRF-TOKEN': _csrf(), 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(json => {
            _page     = json.current_page;
            _lastPage = json.last_page;
            _total    = json.total;

            _renderCards(json.data, append);
            _updateCounter(_total);

            // Update or hide sentinel
            if (sentinel) {
                if (_page >= _lastPage) {
                    sentinel.innerHTML = _total > 0
                        ? '<small style="color:#a0aec0">— Fin de resultados —</small>'
                        : '';
                } else {
                    sentinel.innerHTML = '';
                }
            }

            // Show/hide empty state
            const empty = document.getElementById('conf-empty');
            if (empty) empty.style.display = _total === 0 ? '' : 'none';
        })
        .catch(err => {
            console.error('Error cargando confirmaciones:', err);
            if (sentinel) sentinel.innerHTML = '<span style="color:#e53e3e;font-size:.8rem">Error al cargar citas.</span>';
        })
        .finally(() => { _loading = false; });
    }

    function _updateCounter(total) {
        const badge = document.getElementById('conf-total-badge');
        if (badge) badge.textContent = total;

        // Actualizar counter del botón activo
        const countEl = document.getElementById(`conf-filtro-count-${_filtro}`);
        if (countEl) countEl.textContent = total;
    }

    // ── Render cards ──────────────────────────────────────────────────────────
    function _renderCards(citas, append) {
        const container = document.getElementById('conf-cards');
        if (!container) return;

        if (!append) container.innerHTML = '';

        if (!citas || citas.length === 0) {
            if (!append) {
                // empty state handled by sentinel check above
            }
            return;
        }

        const html = citas.map(c => _buildCard(c)).join('');
        container.insertAdjacentHTML('beforeend', html);

        // Attach event listeners for new cards
        _attachCardEvents(container, citas);
    }

    function _buildCard(c) {
        // Patient name
        let nombre = '';
        let telefono = '';
        let email = '';
        let isLead = false;

        if (c.paciente && c.paciente.persona) {
            const p = c.paciente.persona;
            nombre   = _esc((p.nombre || '') + ' ' + (p.apellido || '')).trim();
            telefono = _esc(p.contacto || '');
            email    = _esc(p.email || '');
        } else {
            nombre   = _esc(c.nombre_lead || 'Sin nombre');
            telefono = _esc(c.telefono_lead || '');
            isLead   = true;
        }

        const especialista = c.especialista && c.especialista.persona
            ? _esc((c.especialista.persona.nombre || '') + ' ' + (c.especialista.persona.apellido || '')).trim()
            : '—';

        const sucursal  = c.sucursal ? _esc(c.sucursal.nombre) : '—';
        const servicio  = c.servicio ? _esc(c.servicio.nombre) : null;
        const estatus   = c.estatus || 'pendiente';
        const fecha     = c.fecha ? (typeof c.fecha === 'string' ? c.fecha.substring(0,10) : '') : '';
        const horaIni   = _formatHora(c.hora_inicio);
        const horaFin   = _formatHora(c.hora_fin);
        const fechaLabel = _formatFecha(fecha);

        const subLine = [telefono, email].filter(Boolean).join(' · ');

        return `
<div class="conf-card" data-id="${c.id}" data-estatus="${_esc(estatus)}">
    <div class="conf-card-inner">
        <div class="conf-card-header">
            <div>
                <div class="conf-patient-name">${nombre || 'Sin nombre'}</div>
                ${subLine ? `<div class="conf-patient-sub">${subLine}</div>` : ''}
                ${isLead ? `<span class="conf-lead-tag"><i class="ti ti-tag" style="font-size:.65rem"></i> Lead</span>` : ''}
            </div>
            <span class="conf-badge-${_esc(estatus)}">${_labelEstatus(estatus)}</span>
        </div>
        <div class="conf-card-body">
            <div class="conf-info-row">
                <i class="ti ti-calendar"></i>
                <strong>${fechaLabel}</strong>
            </div>
            <div class="conf-info-row">
                <i class="ti ti-clock"></i>
                ${horaIni} – ${horaFin}
            </div>
            <div class="conf-info-row">
                <i class="ti ti-stethoscope"></i>
                ${especialista}
            </div>
            ${servicio ? `<div class="conf-info-row"><i class="ti ti-clipboard-list"></i> ${servicio}</div>` : ''}
            <div class="conf-info-row">
                <i class="ti ti-building-store"></i>
                ${sucursal}
            </div>
            ${c.motivo ? `<div class="conf-info-row"><i class="ti ti-notes"></i> ${_esc(c.motivo)}</div>` : ''}
        </div>
        <div class="conf-card-actions">
            ${_buildBotonesAccion(c.id, c, fecha, horaIni, horaFin)}
        </div>
    </div>
</div>`;
    }

    /** Genera los botones de acción según el filtro activo */
    function _buildBotonesAccion(id, c, fecha, horaIni, horaFin) {
        const trasladar = `<button class="conf-btn-trasladar"
            data-id="${id}"
            data-esp-id="${c.especialista_id || ''}"
            data-fecha="${fecha}"
            data-hora-ini="${horaIni}"
            data-hora-fin="${horaFin}"><i class="ti ti-calendar-event"></i> Trasladar</button>`;

        const editar = `<button class="conf-btn-editar" data-id="${id}"><i class="ti ti-edit"></i> Editar</button>`;

        const actividad = `<button class="conf-btn-actividad" data-id="${id}" data-nombre="${_esc(_getNombreCard(c))}"><i class="ti ti-history"></i></button>`;

        if (_filtro === 'confirmadas') {
            return `<button class="conf-btn-rechazar" data-id="${id}"><i class="ti ti-x"></i> Cancelar</button>${trasladar}${editar}${actividad}`;
        }
        if (_filtro === 'rechazadas') {
            return `<button class="conf-btn-confirmar" data-id="${id}"><i class="ti ti-check"></i> Confirmar</button>${trasladar}${editar}${actividad}`;
        }
        // pendientes (default)
        return `<button class="conf-btn-confirmar" data-id="${id}"><i class="ti ti-check"></i> Confirmar</button>${trasladar}${editar}${actividad}`;
    }

    function _getNombreCard(c) {
        if (c.paciente && c.paciente.persona) {
            const p = c.paciente.persona;
            return ((p.nombre || '') + ' ' + (p.apellido || '')).trim();
        }
        return c.nombre_lead || 'Sin nombre';
    }

    function _attachCardEvents(container, citas) {
        // Build a quick lookup map for cita data
        const citaMap = {};
        citas.forEach(c => { citaMap[c.id] = c; });

        // Confirmar buttons
        container.querySelectorAll('.conf-btn-confirmar[data-id]').forEach(btn => {
            // Only bind once — use a flag
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => _confirmar(btn.dataset.id));
        });

        // Trasladar buttons
        container.querySelectorAll('.conf-btn-trasladar[data-id]').forEach(btn => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                // Fetch full cita data then open trasladar
                fetch(`/citas/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrf() } })
                    .then(r => r.json())
                    .then(data => _trasladar(data))
                    .catch(() => _toast('Error al cargar datos de la cita.', 'error'));
            });
        });

        // Ver actividad buttons
        container.querySelectorAll('.conf-btn-actividad[data-id]').forEach(btn => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => _verActividad(btn.dataset.id, btn.dataset.nombre));
        });

        // Rechazar/Cancelar buttons
        container.querySelectorAll('.conf-btn-rechazar[data-id]').forEach(btn => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => _cancelar(btn.dataset.id));
        });

        // Editar buttons
        container.querySelectorAll('.conf-btn-editar[data-id]').forEach(btn => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => _editar(btn.dataset.id));
        });
    }

    // ── Confirm action ────────────────────────────────────────────────────────
    function _confirmar(id) {
        fetch(`/citas/${id}/estatus`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': _csrf(),
            },
            body: JSON.stringify({ estatus: 'confirmada' }),
        })
        .then(r => r.json())
        .then(json => {
            if (json.success) {
                const card = document.querySelector(`.conf-card[data-id="${id}"]`);
                if (card) {
                    card.classList.add('removing');
                    setTimeout(() => {
                        card.remove();
                        _total = Math.max(0, _total - 1);
                        _updateCounter(_total);
                        const empty = document.getElementById('conf-empty');
                        if (empty && document.querySelectorAll('.conf-card').length === 0) {
                            empty.style.display = '';
                        }
                    }, 320);
                }
                _toast('Cita confirmada correctamente.');
            } else {
                _toast(json.mensaje || 'Error al confirmar.', 'error');
            }
        })
        .catch(() => _toast('Error de red al confirmar la cita.', 'error'));
    }

    // ── Trasladar action ──────────────────────────────────────────────────────
    function _trasladar(cita) {
        _citaActual = cita;

        // Fill specialist select
        const espSel = document.getElementById('tr-especialista');
        if (espSel) {
            espSel.value = cita.especialista_id || '';
            // If select2 is initialized
            if (typeof $ !== 'undefined' && $(espSel).data('select2')) {
                $(espSel).val(cita.especialista_id || '').trigger('change');
            }
        }

        // Reset cal & slots
        TrasladarCal.init(cita.especialista_id, cita.fecha ? cita.fecha.substring(0,10) : null);

        // Show modal
        const modalEl = document.getElementById('modal-trasladar');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    // ── Edit action ───────────────────────────────────────────────────────────
    function _editar(id) {
        fetch(`/citas/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrf() } })
            .then(r => r.json())
            .then(cita => {
                _abrirModalEditar(cita);
            })
            .catch(() => _toast('Error al cargar datos de la cita.', 'error'));
    }

    function _abrirModalEditar(cita) {
        // The edit modal (#modal-crear-cita) needs to be pre-filled.
        // We dispatch a custom event that citas.module.js can intercept,
        // or we fill it directly if the form elements exist on this page.
        const form = document.getElementById('form-crear-cita');
        if (!form) { _toast('Modal de edición no disponible.', 'error'); return; }

        // Set hidden cita id
        let citaIdInput = form.querySelector('[name="cita_id"]');
        if (!citaIdInput) {
            citaIdInput = document.createElement('input');
            citaIdInput.type = 'hidden';
            citaIdInput.name = 'cita_id';
            form.appendChild(citaIdInput);
        }
        citaIdInput.value = cita.id;

        // Fill form fields
        _setFormVal(form, 'especialista_id', cita.especialista_id);
        _setFormVal(form, 'sucursal_id', cita.sucursal_id);
        _setFormVal(form, 'servicio_id', cita.servicio_id || '');
        _setFormVal(form, 'estatus', cita.estatus);
        _setFormVal(form, 'origen', cita.origen || 'web');
        _setFormVal(form, 'motivo', cita.motivo || '');
        _setFormVal(form, 'observaciones', cita.observaciones || '');
        _setFormVal(form, 'nombre_lead', cita.nombre_lead || '');
        _setFormVal(form, 'telefono_lead', cita.telefono_lead || '');

        // Show all blocks that are normally hidden
        ['cc-bloque-calendario','cc-bloque-paciente','cc-bloque-servicio',
         'cc-bloque-lead1','cc-bloque-lead2','cc-bloque-estatus','cc-bloque-origen',
         'cc-bloque-motivo','cc-bloque-obs','cc-btn-guardar'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = '';
        });

        // Set fecha/hora hidden inputs
        const fechaStr = cita.fecha ? (typeof cita.fecha === 'string' ? cita.fecha.substring(0,10) : '') : '';
        _setInputVal('cc-fecha-hidden', fechaStr);
        _setInputVal('cc-hora-inicio-hidden', _formatHora(cita.hora_inicio));
        _setInputVal('cc-hora-fin-hidden', _formatHora(cita.hora_fin));

        // Update modal title
        const label = document.getElementById('modal-crear-cita-label');
        if (label) label.innerHTML = '<i class="ti ti-edit me-2 text-primary"></i>Editar Cita';

        // Store cita id on form for submit handler
        form.dataset.citaId = cita.id;

        const modalEl = document.getElementById('modal-crear-cita');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    function _setFormVal(form, name, val) {
        const el = form.querySelector(`[name="${name}"]`);
        if (el) el.value = val ?? '';
    }

    function _setInputVal(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    }

    // ── Ver actividad ─────────────────────────────────────────────────────────
    function _verActividad(id, nombre) {
        // Update modal subtitle
        const subtitleEl = document.getElementById('conf-act-paciente');
        if (subtitleEl) subtitleEl.textContent = nombre || '';

        // Open modal first
        const modalEl = document.getElementById('modal-conf-actividad');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        // Init LogActividad
        if (typeof LogActividad !== 'undefined') {
            LogActividad.init({
                uid:      'la-conf-act',
                endpoint: `/log-actividad/citas/${id}`,
                csrf:     _csrf(),
            });
        }
    }

    // ── Cancel action ─────────────────────────────────────────────────────────
    function _cancelar(id) {
        fetch(`/citas/${id}/estatus`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': _csrf(),
            },
            body: JSON.stringify({ estatus: 'cancelada' }),
        })
        .then(r => r.json())
        .then(json => {
            if (json.success) {
                const card = document.querySelector(`.conf-card[data-id="${id}"]`);
                if (card) {
                    card.classList.add('removing');
                    setTimeout(() => {
                        card.remove();
                        _total = Math.max(0, _total - 1);
                        _updateCounter(_total);
                        const empty = document.getElementById('conf-empty');
                        if (empty && document.querySelectorAll('.conf-card').length === 0) {
                            empty.style.display = '';
                        }
                    }, 320);
                }
                _toast('Cita cancelada correctamente.');
            } else {
                _toast(json.mensaje || 'Error al cancelar.', 'error');
            }
        })
        .catch(() => _toast('Error de red al cancelar la cita.', 'error'));
    }

    // ── Filter buttons ────────────────────────────────────────────────────────
    function _setupFiltros() {
        document.querySelectorAll('.conf-filtro-btn[data-filtro]').forEach(btn => {
            btn.addEventListener('click', function () {
                const nuevoFiltro = this.dataset.filtro;
                if (nuevoFiltro === _filtro) return;

                // Toggle active class
                document.querySelectorAll('.conf-filtro-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                _filtro = nuevoFiltro;
                _page   = 1;
                _query  = '';

                // Clear search input
                const searchInput = document.getElementById('conf-search');
                if (searchInput) searchInput.value = '';

                // Reset cards
                const container = document.getElementById('conf-cards');
                if (container) container.innerHTML = '';

                // Hide empty state while loading
                const empty = document.getElementById('conf-empty');
                if (empty) empty.style.display = 'none';

                _cargarPagina(1, '', false);
            });
        });
    }

    // ── Infinite scroll setup ─────────────────────────────────────────────────
    function _setupObserver() {
        const sentinel = document.getElementById('conf-sentinel');
        if (!sentinel || !window.IntersectionObserver) return;

        _observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !_loading && _page < _lastPage) {
                    _cargarPagina(_page + 1, _query, true);
                }
            });
        }, { rootMargin: '100px' });

        _observer.observe(sentinel);
    }

    // ── Search debounce ───────────────────────────────────────────────────────
    function _setupSearch() {
        const input = document.getElementById('conf-search');
        if (!input) return;

        input.addEventListener('input', function () {
            clearTimeout(_debounceTimer);
            _debounceTimer = setTimeout(() => {
                _query  = this.value.trim();
                _page   = 1;
                _cargarPagina(1, _query, false);
            }, 320);
        });
    }

    // ── Trasladar form submit ─────────────────────────────────────────────────
    function _setupTrasladarForm() {
        const form = document.getElementById('form-trasladar');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!_citaActual) return;

            const fecha     = document.getElementById('tr-fecha-hidden')?.value;
            const horaIni   = document.getElementById('tr-hora-inicio-hidden')?.value;
            const horaFin   = document.getElementById('tr-hora-fin-hidden')?.value;
            const espId     = document.getElementById('tr-especialista')?.value;

            if (!fecha || !horaIni || !horaFin) {
                _toast('Selecciona una fecha y un horario.', 'error');
                return;
            }

            // Build payload: keep existing cita fields, override fecha/hora
            const payload = {
                especialista_id: espId || _citaActual.especialista_id,
                sucursal_id:     _citaActual.sucursal_id,
                paciente_id:     _citaActual.paciente_id || null,
                caso_id:         _citaActual.caso_id || null,
                servicio_id:     _citaActual.servicio_id || null,
                nombre_lead:     _citaActual.nombre_lead || null,
                telefono_lead:   _citaActual.telefono_lead || null,
                fecha:           fecha,
                hora_inicio:     horaIni,
                hora_fin:        horaFin,
                estatus:         _citaActual.estatus,
                motivo:          _citaActual.motivo || null,
                observaciones:   _citaActual.observaciones || null,
                origen:          _citaActual.origen || 'web',
            };

            const btn = form.querySelector('[type="submit"]');
            if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }

            fetch(`/citas/${_citaActual.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': _csrf(),
                },
                body: JSON.stringify(payload),
            })
            .then(r => r.json())
            .then(json => {
                if (json.success) {
                    // Close modal
                    const modalEl = document.getElementById('modal-trasladar');
                    if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();

                    _toast('Cita trasladada correctamente.');

                    // Refresh list from page 1
                    setTimeout(() => {
                        _page = 1;
                        _cargarPagina(1, _query, false);
                    }, 400);
                } else {
                    _toast(json.mensaje || json.message || 'Error al trasladar.', 'error');
                }
            })
            .catch(() => _toast('Error de red al trasladar la cita.', 'error'))
            .finally(() => {
                if (btn) { btn.disabled = false; btn.textContent = 'Guardar traslado'; }
            });
        });
    }

    // ── Edit form submit override ─────────────────────────────────────────────
    function _setupEditForm() {
        const form = document.getElementById('form-crear-cita');
        if (!form) return;

        // Override or supplement the existing submit handler
        // We add a listener that only fires when form.dataset.citaId is set
        form.addEventListener('submit', function (e) {
            const citaId = form.dataset.citaId;
            if (!citaId) return; // Let the original handler deal with new citas

            e.preventDefault();
            e.stopImmediatePropagation();

            const data = Object.fromEntries(new FormData(form).entries());

            // Remove our helper field
            delete data.cita_id;

            const btn = document.getElementById('cc-btn-guardar');
            if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }

            fetch(`/citas/${citaId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': _csrf(),
                },
                body: JSON.stringify(data),
            })
            .then(r => r.json())
            .then(json => {
                if (json.success) {
                    const modalEl = document.getElementById('modal-crear-cita');
                    if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
                    form.dataset.citaId = '';
                    _toast('Cita actualizada correctamente.');
                    setTimeout(() => {
                        _page = 1;
                        _cargarPagina(1, _query, false);
                    }, 400);
                } else {
                    _toast(json.mensaje || json.message || 'Error al actualizar.', 'error');
                }
            })
            .catch(() => _toast('Error de red al actualizar la cita.', 'error'))
            .finally(() => {
                if (btn) { btn.disabled = false; btn.textContent = 'Guardar Cita'; }
            });
        }, true); // capture phase so it runs before possible original handler
    }

    // ── Specialist change in trasladar modal ──────────────────────────────────
    function _setupTrasladarEspChange() {
        const sel = document.getElementById('tr-especialista');
        if (!sel) return;

        sel.addEventListener('change', function () {
            const espId = this.value;
            if (espId) {
                TrasladarCal.cambiarEspecialista(espId);
            }
        });
    }

    // ── Public init ───────────────────────────────────────────────────────────
    function init() {
        _setupFiltros();
        _cargarPagina(1, '', false);
        _setupObserver();
        _setupSearch();
        _setupTrasladarForm();
        _setupEditForm();
        _setupTrasladarEspChange();
    }

    return { init };

})();


/* ============================================================
   TrasladarCal — mini calendar for the trasladar modal
   Standalone implementation, no dependency on citas.module.js
   ============================================================ */
const TrasladarCal = (function () {

    'use strict';

    const MESES_ES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                      'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const DIAS_ES  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

    let _espId       = null;
    let _mesActual   = new Date();
    let _disponib    = {};
    let _fechaSel    = null;
    let _slotSel     = null;
    let _cargandoCal = false;
    let _cargandoSlot = false;
    let _preselFecha = null;

    function _csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function init(espId, preselFecha) {
        _espId      = espId;
        _preselFecha = preselFecha || null;
        _fechaSel   = null;
        _slotSel    = null;
        _disponib   = {};
        _mesActual  = new Date();
        _mesActual.setDate(1);

        // If presel fecha is provided, start on that month
        if (_preselFecha) {
            const [y, m] = _preselFecha.split('-');
            _mesActual = new Date(parseInt(y), parseInt(m) - 1, 1);
        }

        // Clear hidden inputs
        _setVal('tr-fecha-hidden', '');
        _setVal('tr-hora-inicio-hidden', '');
        _setVal('tr-hora-fin-hidden', '');

        // Clear slots panel
        const slotsEl = document.getElementById('tr-slots');
        if (slotsEl) slotsEl.innerHTML = '';
        const slotsPanel = document.getElementById('tr-bloque-slots');
        if (slotsPanel) slotsPanel.style.display = 'none';

        if (_espId) {
            _cargarMes();
        } else {
            const calEl = document.getElementById('tr-calendario');
            if (calEl) calEl.innerHTML = '<div class="tr-cal-loading">Selecciona un especialista.</div>';
        }
    }

    function cambiarEspecialista(espId) {
        _espId = espId;
        _fechaSel = null;
        _slotSel  = null;
        _disponib = {};
        _mesActual = new Date();
        _mesActual.setDate(1);
        _setVal('tr-fecha-hidden', '');
        _setVal('tr-hora-inicio-hidden', '');
        _setVal('tr-hora-fin-hidden', '');
        const slotsPanel = document.getElementById('tr-bloque-slots');
        if (slotsPanel) slotsPanel.style.display = 'none';
        if (_espId) _cargarMes();
    }

    function _setVal(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val;
    }

    function _cargarMes() {
        if (!_espId || _cargandoCal) return;
        _cargandoCal = true;

        const mes = _mesActual.getFullYear() + '-' +
                    String(_mesActual.getMonth() + 1).padStart(2, '0');

        const calEl = document.getElementById('tr-calendario');
        if (calEl) calEl.innerHTML = '<div class="tr-cal-loading"><i class="ti ti-loader-2 me-1"></i>Cargando...</div>';

        fetch(`/citas/disponibilidad/${_espId}?mes=${mes}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrf() }
        })
        .then(r => r.json())
        .then(data => {
            _disponib = data;
            _renderCal();
        })
        .catch(() => {
            if (calEl) calEl.innerHTML = '<div class="tr-cal-loading text-danger">Error al cargar disponibilidad.</div>';
        })
        .finally(() => { _cargandoCal = false; });
    }

    function _renderCal() {
        const calEl = document.getElementById('tr-calendario');
        if (!calEl) return;

        const year  = _mesActual.getFullYear();
        const month = _mesActual.getMonth();
        const titulo = MESES_ES[month] + ' ' + year;

        const primerDia = new Date(year, month, 1);
        const startCol  = (primerDia.getDay() + 6) % 7;
        const diasEnMes = new Date(year, month + 1, 0).getDate();
        const hoy       = new Date();
        const hoyStr    = `${hoy.getFullYear()}-${String(hoy.getMonth()+1).padStart(2,'0')}-${String(hoy.getDate()).padStart(2,'0')}`;

        let html = `
        <div class="tr-cal-header">
            <button class="tr-cal-nav" id="tr-cal-prev" type="button">‹</button>
            <span class="tr-cal-title">${titulo}</span>
            <button class="tr-cal-nav" id="tr-cal-next" type="button">›</button>
        </div>
        <div class="tr-cal-dow">
            <span>Lu</span><span>Ma</span><span>Mi</span>
            <span>Ju</span><span>Vi</span><span>Sá</span><span>Do</span>
        </div>
        <div class="tr-cal-days">`;

        for (let i = 0; i < startCol; i++) {
            html += `<div class="tr-cal-day other-month"></div>`;
        }

        for (let d = 1; d <= diasEnMes; d++) {
            const fechaStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const estado   = _disponib[fechaStr] || 'sin_horario';
            const esHoy    = fechaStr === hoyStr;
            const esSel    = fechaStr === _fechaSel;
            const esPasado = new Date(year, month, d) < new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());

            let cls = 'tr-cal-day';
            if (esPasado || estado === 'sin_horario') cls += ' sin-horario';
            else if (estado === 'lleno')              cls += ' lleno';
            else                                      cls += ' disponible';
            if (esHoy) cls += ' hoy';
            if (esSel) cls += ' seleccionado';

            html += `<div class="${cls}" data-fecha="${fechaStr}">${d}</div>`;
        }

        html += `</div>
        <div class="tr-leyenda">
            <span><i class="tr-dot tr-dot-disponible"></i>Disponible</span>
            <span><i class="tr-dot tr-dot-lleno"></i>Lleno</span>
            <span><i class="tr-dot tr-dot-sin"></i>Sin horario</span>
        </div>`;

        calEl.innerHTML = html;

        // Nav buttons
        document.getElementById('tr-cal-prev')?.addEventListener('click', () => {
            _mesActual.setMonth(_mesActual.getMonth() - 1);
            _cargarMes();
        });
        document.getElementById('tr-cal-next')?.addEventListener('click', () => {
            _mesActual.setMonth(_mesActual.getMonth() + 1);
            _cargarMes();
        });

        // Day click
        calEl.querySelectorAll('.tr-cal-day.disponible').forEach(el => {
            el.addEventListener('click', () => _seleccionarFecha(el.dataset.fecha));
        });

        // Auto-preselect if preselFecha is in this month
        if (_preselFecha) {
            const fechaDisp = _disponib[_preselFecha];
            if (fechaDisp === 'disponible' || fechaDisp === 'lleno') {
                // Highlight but don't load slots automatically
                const dayEl = calEl.querySelector(`[data-fecha="${_preselFecha}"]`);
                if (dayEl && dayEl.classList.contains('disponible')) {
                    // Pre-select silently
                    _fechaSel = _preselFecha;
                    dayEl.classList.add('seleccionado');
                }
            }
            _preselFecha = null;
        }
    }

    function _seleccionarFecha(fecha) {
        _fechaSel = fecha;
        _slotSel  = null;
        _setVal('tr-fecha-hidden', fecha);
        _setVal('tr-hora-inicio-hidden', '');
        _setVal('tr-hora-fin-hidden', '');

        // Highlight selected day
        document.querySelectorAll('#tr-calendario .tr-cal-day').forEach(el => {
            el.classList.remove('seleccionado');
            if (el.dataset.fecha === fecha) el.classList.add('seleccionado');
        });

        // Show slots panel
        const slotsPanel = document.getElementById('tr-bloque-slots');
        if (slotsPanel) slotsPanel.style.display = '';

        // Update fecha label
        const [y, m, d] = fecha.split('-');
        const dt = new Date(parseInt(y), parseInt(m)-1, parseInt(d));
        const label = DIAS_ES[dt.getDay()] + ' ' + parseInt(d) + ' de ' + MESES_ES[dt.getMonth()] + ' ' + y;
        const fechaLegible = document.getElementById('tr-fecha-legible');
        if (fechaLegible) fechaLegible.textContent = label;

        _cargarSlots(fecha);
    }

    function _cargarSlots(fecha) {
        if (_cargandoSlot) return;
        _cargandoSlot = true;

        const slotsEl = document.getElementById('tr-slots');
        if (slotsEl) slotsEl.innerHTML = '<div class="tr-slots-loading"><i class="ti ti-loader-2 me-1"></i>Cargando horas...</div>';

        fetch(`/citas/horas-disponibles/${_espId}?fecha=${fecha}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrf() }
        })
        .then(r => r.json())
        .then(slots => _renderSlots(slots))
        .catch(() => {
            if (slotsEl) slotsEl.innerHTML = '<div class="tr-slots-loading text-danger">Error al cargar horas.</div>';
        })
        .finally(() => { _cargandoSlot = false; });
    }

    function _renderSlots(slots) {
        const slotsEl = document.getElementById('tr-slots');
        if (!slotsEl) return;

        if (!slots.length) {
            slotsEl.innerHTML = '<div class="tr-slots-loading text-muted">No hay horario para este día.</div>';
            return;
        }

        slotsEl.innerHTML = slots.map(s => {
            const cls = s.disponible ? 'tr-slot' : 'tr-slot ocupado';
            return `<button type="button" class="${cls}" data-hi="${s.hora_inicio}" data-hf="${s.hora_fin}">
                        ${s.hora_inicio} – ${s.hora_fin}
                    </button>`;
        }).join('');

        slotsEl.querySelectorAll('.tr-slot:not(.ocupado)').forEach(btn => {
            btn.addEventListener('click', () => {
                slotsEl.querySelectorAll('.tr-slot').forEach(b => b.classList.remove('seleccionado'));
                btn.classList.add('seleccionado');
                _slotSel = { hi: btn.dataset.hi, hf: btn.dataset.hf };
                _setVal('tr-hora-inicio-hidden', btn.dataset.hi);
                _setVal('tr-hora-fin-hidden', btn.dataset.hf);
            });
        });
    }

    return { init, cambiarEspecialista };

})();


// ── Bootstrap on DOM ready ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    ConfirmacionModule.init();
});
