/**
 * CitasModule — citas.module.js
 * Calendario con 3 vistas: Día, Semana, Recursos (custom grid por especialista).
 * Sin dependencias externas adicionales a FullCalendar y Bootstrap.
 */
const CitasModule = (function () {

    // ── Estado ────────────────────────────────────────────────────────────────
    let CONFIG        = {};
    let calendar      = null;
    let vista         = 'semana';      // 'dia' | 'semana' | 'recursos'
    let fechaActual   = new Date();
    let filtroEsps    = new Set();     // IDs activos; vacío = todos
    let filtroTexto   = '';
    let filtroSucursal = '';           // ID sucursal; vacío = todas
    let modoAjustado  = false;         // true = todos los esp. caben en pantalla
    let startHour     = 9;            // hora inicio visible (se ajusta por sucursal)
    let endHour       = 18;           // hora fin visible (se ajusta por sucursal)

    const COLORES = {
        pendiente:  { bg: '#94a3b8', border: '#64748b', text: '#ffffff' },
        confirmada: { bg: '#38a169', border: '#2f8a59', text: '#ffffff' },
        atendida:   { bg: '#4a5568', border: '#2d3748', text: '#ffffff' },
        cancelada:  { bg: '#e53e3e', border: '#c53030', text: '#ffffff' },
        no_asistio: { bg: '#dd6b20', border: '#c05621', text: '#ffffff' },
    };

    // START_HOUR / END_HOUR ahora son dinámicos (alias para el grid de recursos)
    const _getStartHour = () => startHour;
    const _getEndHour   = () => endHour;
    const PX_PER_MIN = 1;   // 1px = 1 minuto

    // ── Inicialización ────────────────────────────────────────────────────────

    function init(cfg) {
        CONFIG = cfg;
        _aplicarHorarioSucursal('');   // calcular rango inicial con todas las sucursales
        _initFullCalendar();
        _initSidebar();
        _initNavegacion();
        _initVistaTabs();
        _initBtnAjustar();
        _initModalCrear();
        _bindEventosDelegados();
        _actualizarDisplayFecha();
    }

    // ── FullCalendar ──────────────────────────────────────────────────────────

    function _initFullCalendar() {
        const el = document.getElementById('calendar');
        if (!el) return;

        calendar = new FullCalendar.Calendar(el, {
            locale:           'es',
            initialView:      'timeGridWeek',
            headerToolbar:    false,
            height:           'auto',
            nowIndicator:     true,
            selectable:       true,
            selectMirror:     true,
            allDaySlot:       false,
            slotMinTime:      String(_getStartHour()).padStart(2,'0') + ':00:00',
            slotMaxTime:      String(_getEndHour()).padStart(2,'0')   + ':00:00',
            eventTimeFormat:  { hour: '2-digit', minute: '2-digit', hour12: false },

            events: _fetchEventosFC,

            select: function (info) {
                _abrirModalCrear(info.startStr, info.endStr);
            },

            eventClick: function (info) {
                _abrirModalVer(info.event.id);
            },

            eventDidMount: function (info) {
                const p = info.event.extendedProps;
                info.el.title = [
                    info.event.title,
                    p.especialista  || '',
                    p.servicio      || '',
                    'Estado: ' + (p.estatus || ''),
                ].filter(Boolean).join('\n');
            },
        });

        calendar.render();
    }

    function _fetchEventosFC(info, successCb, failureCb) {
        const params = new URLSearchParams({
            start: info.startStr.slice(0, 10),
            end:   info.endStr.slice(0, 10),
        });

        if (filtroEsps.size > 0) params.append('especialistas', [...filtroEsps].join(','));
        if (filtroTexto)         params.append('q', filtroTexto);
        if (filtroSucursal)      params.append('sucursal_id', filtroSucursal);

        fetch(`${CONFIG.eventosUrl}?${params}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CONFIG.csrf }
        })
        .then(r => r.json())
        .then(data => {
            _actualizarConteoSidebar(data);
            successCb(data);
        })
        .catch(failureCb);
    }

    // ── Vista Tabs ────────────────────────────────────────────────────────────

    function _initVistaTabs() {
        document.querySelectorAll('.citas-view-tab').forEach(btn => {
            btn.addEventListener('click', function () {
                _switchVista(this.dataset.view);
            });
        });
    }

    function _switchVista(nuevaVista) {
        vista = nuevaVista;

        document.querySelectorAll('.citas-view-tab').forEach(b => {
            b.classList.toggle('active', b.dataset.view === vista);
        });

        const fcWrap   = document.getElementById('fc-container');
        const recWrap  = document.getElementById('recursos-container');
        const btnAjust = document.getElementById('btn-ajustar');

        if (vista === 'recursos') {
            if (fcWrap)   fcWrap.style.display   = 'none';
            if (recWrap)  recWrap.style.display  = 'block';
            if (btnAjust) btnAjust.style.display = 'inline-flex';
            _renderRecursos();
        } else {
            if (fcWrap)   fcWrap.style.display   = 'block';
            if (recWrap)  recWrap.style.display  = 'none';
            if (btnAjust) btnAjust.style.display = 'none';
            calendar.changeView(vista === 'dia' ? 'timeGridDay' : 'timeGridWeek');
            calendar.gotoDate(fechaActual);
        }

        _actualizarDisplayFecha();
    }

    // ── Botón Ajustar ─────────────────────────────────────────────────────────

    function _initBtnAjustar() {
        document.getElementById('btn-ajustar')?.addEventListener('click', function () {
            modoAjustado = !modoAjustado;
            this.classList.toggle('active', modoAjustado);
            // Actualizar tooltip/texto del botón
            const span = this.querySelector('span');
            if (span) span.textContent = modoAjustado ? 'Expandir' : 'Ajustar';
            _renderRecursos();
        });
    }

    // ── Navegación de fecha ───────────────────────────────────────────────────

    function _initNavegacion() {
        document.getElementById('btn-hoy')?.addEventListener('click', () => {
            fechaActual = new Date();
            _navegar();
        });
        document.getElementById('btn-prev')?.addEventListener('click', () => {
            _moverFecha(-1);
        });
        document.getElementById('btn-next')?.addEventListener('click', () => {
            _moverFecha(1);
        });
    }

    function _moverFecha(delta) {
        if (vista === 'dia' || vista === 'recursos') {
            fechaActual.setDate(fechaActual.getDate() + delta);
        } else {
            fechaActual.setDate(fechaActual.getDate() + delta * 7);
        }
        _navegar();
    }

    function _navegar() {
        if (vista === 'recursos') {
            _renderRecursos();
        } else {
            calendar.gotoDate(fechaActual);
        }
        _actualizarDisplayFecha();
    }

    function _actualizarDisplayFecha() {
        const el = document.getElementById('citas-date-display');
        if (!el) return;

        const opts = { locale: 'es-PA' };
        if (vista === 'semana') {
            const inicio = _inicioSemana(fechaActual);
            const fin    = new Date(inicio);
            fin.setDate(fin.getDate() + 6);
            el.textContent = `${inicio.toLocaleDateString('es-PA', { day: 'numeric', month: 'short' })} — ${fin.toLocaleDateString('es-PA', { day: 'numeric', month: 'short', year: 'numeric' })}`;
        } else {
            el.textContent = fechaActual.toLocaleDateString('es-PA', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
    }

    function _inicioSemana(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
        d.setDate(diff);
        return d;
    }

    // ── Vista Recursos (grid custom) ──────────────────────────────────────────

    function _renderRecursos() {
        const recWrap = document.getElementById('recursos-container');
        if (!recWrap) return;

        const fecha = fechaActual.toISOString().slice(0, 10);

        // Mostrar loading
        recWrap.innerHTML = `
            <div class="d-flex justify-content-center align-items-center" style="height:300px;">
                <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem;"></div>
            </div>`;

        const params = new URLSearchParams({ start: fecha, end: fecha });
        if (filtroEsps.size > 0) params.append('especialistas', [...filtroEsps].join(','));
        if (filtroTexto)         params.append('q', filtroTexto);
        if (filtroSucursal)      params.append('sucursal_id', filtroSucursal);

        fetch(`${CONFIG.eventosUrl}?${params}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CONFIG.csrf }
        })
        .then(r => r.json())
        .then(eventos => {
            _actualizarConteoSidebar(eventos);
            _construirGridRecursos(recWrap, eventos, fecha);
        })
        .catch(() => {
            recWrap.innerHTML = '<div class="text-center text-muted py-5">Error al cargar las citas.</div>';
        });
    }

    function _construirGridRecursos(container, eventos, fecha) {
        // ── Modo normal: 160px fijos. Modo ajustado: ancho = contenido vertical ──
        const colW = modoAjustado ? null : 160; // null = sin width inline, CSS lo controla

        if (modoAjustado) {
            container.classList.add('rec-ajustado');
            container.style.removeProperty('--rec-col-w');
        } else {
            container.classList.remove('rec-ajustado');
        }

        // Agrupar por especialista
        const espMap = {};
        eventos.forEach(ev => {
            const espNombre = ev.extendedProps?.especialista || 'Sin asignar';
            const espId     = ev.extendedProps?.especialista_id || 0;
            if (!espMap[espId]) {
                espMap[espId] = { nombre: espNombre, eventos: [] };
            }
            espMap[espId].eventos.push(ev);
        });

        // Si no hay nadie con citas, mostrar todos los especialistas del sidebar
        const espItems = document.querySelectorAll('.esp-sidebar-item');
        espItems.forEach(item => {
            const id = item.dataset.id;
            if (!espMap[id] && (filtroEsps.size === 0 || filtroEsps.has(id))) {
                const nombre = item.querySelector('.esp-sidebar-name')?.textContent || '';
                espMap[id] = { nombre, eventos: [] };
            }
        });

        const especialistas = Object.entries(espMap);
        const horasTotales  = _getEndHour() - _getStartHour();

        // ── Headers ─────────────────────────────
        let headersHTML = `<div class="rec-time-spacer"></div><div class="rec-esp-headers">`;
        especialistas.forEach(([id, esp]) => {
            const iniciales = esp.nombre.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
            const widthStyle = colW ? `width:${colW}px;` : '';
            // En modo ajustado: sin avatar (ocupa ancho innecesario)
            const avatarHTML = modoAjustado ? '' : `<div class="rec-esp-avatar">${iniciales}</div>`;
            headersHTML += `
                <div class="rec-esp-header" style="${widthStyle}">
                    ${avatarHTML}
                    <div class="rec-esp-info">
                        <div class="rec-esp-name">${esp.nombre}</div>
                        <div class="rec-esp-count">${esp.eventos.length} cita${esp.eventos.length !== 1 ? 's' : ''}</div>
                    </div>
                </div>`;
        });
        headersHTML += `</div>`;

        // ── Time column ──────────────────────────
        let timeColHTML = '';
        for (let h = _getStartHour(); h < _getEndHour(); h++) {
            timeColHTML += `<div class="rec-time-slot">${String(h).padStart(2, '0')}:00</div>`;
        }

        // ── Especialista columns ─────────────────
        let espColsHTML = '';
        const gridHeight = horasTotales * 60 * PX_PER_MIN;

        especialistas.forEach(([id, esp]) => {
            // Líneas de hora
            let linesHTML = '';
            for (let h = 0; h < horasTotales; h++) {
                linesHTML += `<div class="rec-hour-line ${h === 0 ? 'rec-hour-line--full' : ''}" style="top:${h * 60}px;"></div>`;
            }

            // Slots clickeables (cada 30 min)
            let slotsHTML = '';
            for (let m = 0; m < horasTotales * 60; m += 30) {
                const slotH = Math.floor((m + _getStartHour() * 60) / 60);
                const slotM = (m + _getStartHour() * 60) % 60;
                const slotTime = `${String(slotH).padStart(2,'0')}:${String(slotM).padStart(2,'0')}`;
                slotsHTML += `<div class="rec-click-slot" style="top:${m}px;height:30px;" data-fecha="${fecha}" data-hora="${slotTime}" data-esp-id="${id}"></div>`;
            }

            // Eventos
            let eventosHTML = '';
            esp.eventos.forEach(ev => {
                const top    = _minutosDesdeInicio(ev.start.slice(11, 16));
                const height = Math.max(_duracionMinutos(ev.start.slice(11, 16), ev.end.slice(11, 16)), 20);
                const color  = ev.backgroundColor || COLORES.pendiente.bg;
                eventosHTML += `
                    <div class="rec-event"
                         style="top:${top}px;height:${height}px;background:${color};"
                         data-cita-id="${ev.id}">
                        <div class="rec-event-title">${ev.title}</div>
                        <div class="rec-event-time">${ev.start.slice(11,16)} – ${ev.end.slice(11,16)}</div>
                    </div>`;
            });

            // Indicador hora actual (solo si es hoy)
            let nowHTML = '';
            const hoy = new Date().toISOString().slice(0, 10);
            if (fecha === hoy) {
                const ahora = new Date();
                const nowMin = ahora.getHours() * 60 + ahora.getMinutes() - _getStartHour() * 60;
                if (nowMin >= 0 && nowMin <= horasTotales * 60) {
                    nowHTML = `<div class="rec-now-line" style="top:${nowMin}px;"></div>`;
                }
            }

            const colWStyle = colW ? `width:${colW}px;` : '';
            espColsHTML += `
                <div class="rec-esp-col" style="height:${gridHeight}px;${colWStyle}flex-shrink:0;">
                    ${linesHTML}${slotsHTML}${eventosHTML}${nowHTML}
                </div>`;
        });

        // ── Ensamblar ─────────────────────────────────────────────
        container.innerHTML = `
            <div class="rec-scroll-wrap">
                <div class="rec-headers-row">${headersHTML}</div>
                <div class="rec-grid-body">
                    <div class="rec-time-col">${timeColHTML}</div>
                    <div class="rec-esp-cols">${espColsHTML}</div>
                </div>
            </div>`;

        // ── Modo ajustado: sincronizar anchos header → columna ───
        // El header tiene ancho natural (= font-size del texto vertical + padding).
        // Las columnas del grid deben coincidir exactamente.
        if (modoAjustado) {
            requestAnimationFrame(() => {
                const headers = container.querySelectorAll('.rec-esp-header');
                const cols    = container.querySelectorAll('.rec-esp-col');
                headers.forEach((hdr, i) => {
                    if (cols[i]) cols[i].style.width = hdr.offsetWidth + 'px';
                });
            });
        }
    }

    function _minutosDesdeInicio(timeStr) {
        if (!timeStr) return 0;
        const [h, m] = timeStr.split(':').map(Number);
        return (h * 60 + m - _getStartHour() * 60) * PX_PER_MIN;
    }

    function _duracionMinutos(startStr, endStr) {
        if (!startStr || !endStr) return 30;
        const [sh, sm] = startStr.split(':').map(Number);
        const [eh, em] = endStr.split(':').map(Number);
        return ((eh * 60 + em) - (sh * 60 + sm)) * PX_PER_MIN;
    }

    // ── Sidebar ───────────────────────────────────────────────────────────────

    function _initSidebar() {
        // filtroEsps vacío = mostrar todos.
        // Al hacer click en uno en modo "mostrar todos" → aislar solo ese.
        // Al hacer click en uno ya activo dentro de una selección → deseleccionarlo.
        // Al hacer click en uno inactivo → sumarlo a la selección.
        document.querySelectorAll('.esp-sidebar-item').forEach(item => {
            item.addEventListener('click', function () {
                const id    = this.dataset.id;
                const items = document.querySelectorAll('.esp-sidebar-item');
                const total = items.length;

                if (filtroEsps.size === 0) {
                    // Modo "todos" → aislar solo el clickeado
                    filtroEsps = new Set([id]);
                    items.forEach(el => el.classList.toggle('inactive', el.dataset.id !== id));
                } else if (filtroEsps.has(id)) {
                    // Estaba activo → deseleccionarlo
                    filtroEsps.delete(id);
                    this.classList.add('inactive');
                    // Si quedó vacío → volver a "todos"
                    if (filtroEsps.size === 0) {
                        items.forEach(el => el.classList.remove('inactive'));
                    }
                } else {
                    // Estaba inactivo → sumarlo
                    filtroEsps.add(id);
                    this.classList.remove('inactive');
                    // Si ya están todos activos → quitar filtro
                    if (filtroEsps.size === total) {
                        filtroEsps.clear();
                    }
                }

                _refrescar();
            });
        });

        // Botón "Todos"
        document.getElementById('btn-sidebar-todos')?.addEventListener('click', function () {
            filtroEsps.clear();
            document.querySelectorAll('.esp-sidebar-item').forEach(i => i.classList.remove('inactive'));
            _refrescar();
        });

        // Presets de vista rápida
        document.querySelectorAll('.sidebar-preset').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.sidebar-preset').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const preset = this.dataset.preset;
                const hoy = new Date();
                if (preset === 'hoy') {
                    fechaActual = new Date(hoy);
                    _switchVista('dia');
                } else if (preset === 'manana') {
                    fechaActual = new Date(hoy);
                    fechaActual.setDate(fechaActual.getDate() + 1);
                    _switchVista('dia');
                } else if (preset === 'semana') {
                    fechaActual = new Date(hoy);
                    _switchVista('semana');
                } else if (preset === 'recursos') {
                    fechaActual = new Date(hoy);
                    _switchVista('recursos');
                }
                _actualizarDisplayFecha();
            });
        });

        // Búsqueda
        document.getElementById('sidebar-search')?.addEventListener('input', function () {
            filtroTexto = this.value.trim();
            clearTimeout(this._timer);
            this._timer = setTimeout(() => _refrescar(), 400);
        });

        // Filtro sucursal
        document.getElementById('filtro-sucursal')?.addEventListener('change', function () {
            filtroSucursal = this.value;
            _aplicarHorarioSucursal(filtroSucursal);
            _refrescar();
        });
    }

    function _aplicarHorarioSucursal(sucId) {
        const horarios = CONFIG.sucursalHorario || {};
        let apertura, cierre;

        if (sucId && horarios[sucId]) {
            apertura = horarios[sucId].apertura;
            cierre   = horarios[sucId].cierre;
        } else {
            // Todas las sucursales: la más temprana y la más tarde
            const vals = Object.values(horarios);
            if (!vals.length) { apertura = '09:00'; cierre = '18:00'; }
            else {
                apertura = vals.reduce((min, s) => s.apertura < min ? s.apertura : min, vals[0].apertura);
                cierre   = vals.reduce((max, s) => s.cierre   > max ? s.cierre   : max, vals[0].cierre);
            }
        }

        startHour = parseInt(apertura.split(':')[0]);
        endHour   = parseInt(cierre.split(':')[0]) + (parseInt(cierre.split(':')[1]) > 0 ? 1 : 0);

        // Actualizar FullCalendar si existe
        if (calendar) {
            calendar.setOption('slotMinTime', apertura + ':00');
            calendar.setOption('slotMaxTime', cierre   + ':00');
        }
    }

    function _refrescar() {
        if (vista === 'recursos') {
            _renderRecursos();
        } else {
            calendar?.refetchEvents();
        }
    }

    function _actualizarConteoSidebar(eventos) {
        // Resetear conteos
        document.querySelectorAll('.esp-sidebar-count').forEach(el => el.textContent = '0');

        eventos.forEach(ev => {
            const espId  = ev.extendedProps?.especialista_id;
            const countEl = document.querySelector(`.esp-sidebar-count[data-id="${espId}"]`);
            if (countEl) {
                countEl.textContent = parseInt(countEl.textContent || 0) + 1;
            }
        });
    }

    // ── Modal Crear ───────────────────────────────────────────────────────────

    function _initModalCrear() {
        const form = document.getElementById('form-crear-cita');
        if (!form) return;

        // Resetear modo al cerrar el modal
        const modalEl = document.getElementById('modal-crear-cita');
        modalEl?.addEventListener('hidden.bs.modal', () => {
            delete form.dataset.editId;
            form.reset();
            const titulo = modalEl.querySelector('#modal-crear-cita-label');
            if (titulo) titulo.innerHTML = '<i class="ti ti-calendar-plus me-2 text-primary"></i>Nueva Cita';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.innerHTML = '<i class="ti ti-check me-1"></i>Guardar Cita';
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn     = form.querySelector('[type="submit"]');
            const editId  = form.dataset.editId;
            const isEdit  = !!editId;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

            const url    = isEdit ? `${CONFIG.baseUrl}/${editId}` : CONFIG.storeUrl;
            const body   = new FormData(form);
            if (isEdit) body.append('_method', 'PUT');

            fetch(url, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CONFIG.csrf, 'Accept': 'application/json' },
                body,
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-crear-cita')).hide();
                    _refrescar();
                    _toast(isEdit ? 'Cita actualizada correctamente.' : 'Cita registrada correctamente.', 'success');
                } else {
                    _mostrarErrores(data.errors || {}, form);
                }
            })
            .catch(() => _toast('Error al guardar.', 'danger'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = isEdit
                    ? '<i class="ti ti-check me-1"></i>Actualizar Cita'
                    : '<i class="ti ti-check me-1"></i>Guardar Cita';
            });
        });
    }

    function _abrirModalCrear(start, end) {
        const modal = document.getElementById('modal-crear-cita');
        if (!modal) return;
        if (start) {
            modal.querySelector('[name="fecha"]')?.setAttribute('value', start.slice(0, 10));
            if (start.length > 10) modal.querySelector('[name="hora_inicio"]')?.setAttribute('value', start.slice(11, 16));
            if (end && end.length > 10) modal.querySelector('[name="hora_fin"]')?.setAttribute('value', end.slice(11, 16));
        }
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }

    // ── Modal Ver ─────────────────────────────────────────────────────────────

    function _abrirModalVer(citaId) {
        fetch(`${CONFIG.baseUrl}/${citaId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CONFIG.csrf }
        })
        .then(r => r.json())
        .then(cita => {
            const modal = document.getElementById('modal-ver-cita');
            if (!modal) return;

            // ── Badge de estatus en header ──────────────────────────
            const badge = modal.querySelector('#ver-estatus-badge');
            if (badge) {
                badge.className   = `badge-estatus badge-estatus--${cita.estatus}`;
                badge.textContent = _labelEstatus(cita.estatus);
            }

            // ── Hora + duración (PostgreSQL envía "HH:MM:SS", cortamos a HH:MM) ──
            const hIni = (cita.hora_inicio || '').slice(0, 5);
            const hFin = (cita.hora_fin    || '').slice(0, 5);
            const durMin = _calcDuracion(hIni, hFin);
            _setText(modal, '#ver-hora',     `${hIni} - ${hFin}`);
            _setText(modal, '#ver-duracion', durMin ? `${durMin} min.` : '');

            // ── Fecha con día de semana (fecha llega ISO "2026-03-02T00:00:00Z") ──
            _setText(modal, '#ver-fecha', _formatFechaLarga(cita.fecha));

            // ── Paciente (accessor no en $appends, construimos desde relación) ──
            const p = cita.paciente?.persona;
            const nombrePaciente = p
                ? `${p.nombre} ${p.apellido}`.trim()
                : (cita.nombre_lead || '—');
            _setText(modal, '#ver-paciente', nombrePaciente);

            const linkPaciente = modal.querySelector('#ver-paciente-link');
            if (linkPaciente) {
                linkPaciente.href = cita.paciente_id
                    ? `${CONFIG.baseUrl.replace('citas', 'pacientes')}/${cita.paciente_id}`
                    : '#';
            }

            const linkWa = modal.querySelector('#ver-whatsapp');
            if (linkWa) {
                const tel = p?.contacto || cita.telefono_lead || '';
                const num = tel.replace(/\D/g, '');
                linkWa.href = num ? `https://wa.me/${num}` : '#';
                linkWa.style.display = num ? '' : 'none';
            }

            // ── Servicio + precio ───────────────────────────────────
            _setText(modal, '#ver-servicio-cat', cita.servicio?.nombre ? 'SERVICIO' : '');
            _setText(modal, '#ver-servicio',  cita.servicio?.nombre || 'Sin especificar');

            const precio = parseFloat(cita.servicio?.precio || 0);
            const precioFmt = precio > 0 ? `$ ${precio.toFixed(2)}` : '';
            _setText(modal, '#ver-precio', precioFmt);

            const cobrarWrap = modal.querySelector('#ver-cobrar-wrap');
            if (cobrarWrap) cobrarWrap.style.display = precio > 0 ? '' : 'none';
            _setText(modal, '#ver-cobrar-monto', precioFmt);

            // ── Especialista (construir desde relación, accessor no en $appends) ──
            const esp = cita.especialista;
            const espNombre = esp
                ? [esp.tratamiento, esp.persona?.nombre, esp.persona?.apellido].filter(Boolean).join(' ')
                : '—';
            _setText(modal, '#ver-especialista', espNombre);
            _setText(modal, '#ver-sucursal',     cita.sucursal?.nombre || '—');
            _setText(modal, '#ver-origen',       _labelOrigen(cita.origen));

            // ── Notas ───────────────────────────────────────────────
            const notas   = [cita.motivo, cita.observaciones].filter(Boolean).join('\n');
            const notasEl = modal.querySelector('#ver-notas-block');
            if (notasEl) notasEl.style.display = notas ? '' : 'none';
            _setText(modal, '#ver-motivo-texto', notas || '—');

            // ── Formulario estatus ──────────────────────────────────
            const formEstatus   = modal.querySelector('#form-estatus');
            const selectEstatus = modal.querySelector('#select-estatus');
            if (formEstatus)   formEstatus.dataset.citaId = cita.id;
            if (selectEstatus) selectEstatus.value = cita.estatus;

            // ── Botones footer ──────────────────────────────────────
            const btnEliminar = modal.querySelector('#btn-eliminar-cita');
            const btnEditar   = modal.querySelector('#btn-editar-cita');
            if (btnEliminar) btnEliminar.dataset.citaId = cita.id;
            if (btnEditar)   btnEditar.dataset.citaId   = cita.id;

            // Guardamos los datos para el modal editar
            modal._citaData = cita;

            bootstrap.Modal.getOrCreateInstance(modal).show();
        })
        .catch(() => _toast('No se pudo cargar la cita.', 'danger'));
    }

    // ── Modal Editar (reutiliza el form crear) ────────────────────────────────

    function _abrirModalEditar(cita) {
        const modalVer  = document.getElementById('modal-ver-cita');
        const modalCrear = document.getElementById('modal-crear-cita');
        if (!modalCrear) return;

        bootstrap.Modal.getOrCreateInstance(modalVer).hide();

        const form = modalCrear.querySelector('#form-crear-cita');
        if (!form) return;

        // Cambiar título y modo
        const titulo = modalCrear.querySelector('#modal-crear-cita-label');
        if (titulo) titulo.innerHTML = '<i class="ti ti-pencil me-2 text-primary"></i>Editar Cita';

        form.dataset.editId = cita.id;

        // Precargar campos
        const set = (name, val) => {
            const el = form.querySelector(`[name="${name}"]`);
            if (el) el.value = val ?? '';
        };

        set('especialista_id', cita.especialista_id);
        set('sucursal_id',     cita.sucursal_id);
        set('paciente_id',     cita.paciente_id || '');
        set('servicio_id',     cita.servicio_id || '');
        set('nombre_lead',     cita.nombre_lead || '');
        set('telefono_lead',   cita.telefono_lead || '');
        set('fecha',           cita.fecha?.slice(0, 10) || '');
        set('hora_inicio',     (cita.hora_inicio || '').slice(0, 5));
        set('hora_fin',        (cita.hora_fin    || '').slice(0, 5));
        set('estatus',         cita.estatus || 'pendiente');
        set('origen',          cita.origen || 'web');
        set('motivo',          cita.motivo || '');
        set('observaciones',   cita.observaciones || '');

        const btnSubmit = form.querySelector('[type="submit"]');
        if (btnSubmit) btnSubmit.innerHTML = '<i class="ti ti-check me-1"></i>Actualizar Cita';

        bootstrap.Modal.getOrCreateInstance(modalCrear).show();
    }

    // ── Eventos delegados ─────────────────────────────────────────────────────

    function _bindEventosDelegados() {
        // Click en evento del grid recursos
        document.addEventListener('click', function (e) {
            const recEvento = e.target.closest('.rec-event');
            if (recEvento) {
                const id = recEvento.dataset.citaId;
                if (id) _abrirModalVer(id);
                return;
            }

            // Click en slot vacío del grid recursos
            const clickSlot = e.target.closest('.rec-click-slot');
            if (clickSlot) {
                const fecha = clickSlot.dataset.fecha;
                const hora  = clickSlot.dataset.hora;
                _abrirModalCrear(`${fecha}T${hora}`, `${fecha}T${_addMinutes(hora, 30)}`);
                return;
            }

            // Eliminar cita
            if (e.target.closest('#btn-eliminar-cita')) {
                const id = e.target.closest('#btn-eliminar-cita').dataset.citaId;
                if (!confirm('¿Eliminar esta cita?')) return;
                fetch(`${CONFIG.baseUrl}/${id}`, {
                    method:  'DELETE',
                    headers: { 'X-CSRF-TOKEN': CONFIG.csrf, 'Accept': 'application/json' },
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-ver-cita')).hide();
                        _refrescar();
                        _toast('Cita eliminada.', 'success');
                    }
                });
                return;
            }

            // Editar cita
            if (e.target.closest('#btn-editar-cita')) {
                const modalVer = document.getElementById('modal-ver-cita');
                const cita = modalVer?._citaData;
                if (cita) _abrirModalEditar(cita);
                return;
            }
        });

        // Submit cambio estatus
        document.addEventListener('submit', function (e) {
            if (!e.target.matches('#form-estatus')) return;
            e.preventDefault();
            const form    = e.target;
            const citaId  = form.dataset.citaId;
            const estatus = form.querySelector('#select-estatus')?.value;

            fetch(`${CONFIG.baseUrl}/${citaId}/estatus`, {
                method:  'PATCH',
                headers: { 'X-CSRF-TOKEN': CONFIG.csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body:    JSON.stringify({ estatus }),
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-ver-cita')).hide();
                    _refrescar();
                    _toast('Estatus actualizado.', 'success');
                }
            });
        });
    }

    // ── Utilidades ────────────────────────────────────────────────────────────

    function _setText(ctx, sel, txt) { const el = ctx.querySelector(sel); if (el) el.textContent = txt; }

    function _formatFecha(str) {
        if (!str) return '—';
        const [y, m, d] = str.slice(0,10).split('-');
        return `${d}/${m}/${y}`;
    }

    function _formatFechaLarga(str) {
        if (!str) return '—';
        // Extraer solo YYYY-MM-DD (puede venir como ISO "2026-03-02T00:00:00.000000Z")
        const partes = str.slice(0, 10).split('-').map(Number);
        // Usar constructor local para evitar desfase de timezone
        const d = new Date(partes[0], partes[1] - 1, partes[2]);
        return d.toLocaleDateString('es-PA', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    function _calcDuracion(inicio, fin) {
        if (!inicio || !fin) return null;
        const [sh, sm] = inicio.split(':').map(Number);
        const [eh, em] = fin.split(':').map(Number);
        const diff = (eh * 60 + em) - (sh * 60 + sm);
        return diff > 0 ? diff : null;
    }

    function _labelEstatus(e) {
        return {
            pendiente:  'En espera',
            confirmada: 'Confirmada',
            atendida:   'Atendida',
            cancelada:  'Cancelada',
            no_asistio: 'No asistió',
        }[e] || e;
    }

    function _labelOrigen(o) {
        return { web: 'Web', telefono: 'Teléfono', chatwoot: 'Chatwoot', mobile: 'App Mobile' }[o] || (o || '—');
    }

    function _addMinutes(timeStr, mins) {
        const [h, m] = timeStr.split(':').map(Number);
        const total  = h * 60 + m + mins;
        return `${String(Math.floor(total/60)).padStart(2,'0')}:${String(total%60).padStart(2,'0')}`;
    }

    function _mostrarErrores(errors, form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
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
        document.getElementById(id)?.addEventListener('hidden.bs.toast', () => document.getElementById(id)?.remove());
    }

    return { init };
})();

/* ============================================================
   MiniCalendario — selector de disponibilidad para crear citas
   ============================================================ */
const MiniCal = (function () {

    const DIAS_ES = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    const MESES_ES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                      'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    let _espId        = null;
    let _mesActual    = new Date();      // primer día del mes visible
    let _disponib     = {};              // { 'YYYY-MM-DD': 'disponible'|'lleno'|'sin_horario' }
    let _cargandoCal  = false;
    let _cargandoSlot = false;
    let _fechaSel     = null;
    let _slotSel      = null;

    // ── Autocomplete de pacientes ──────────────────────────────────────────────
    function _initAutocompletePac() {
        const input    = document.getElementById('cc-pac-input');
        const dropdown = document.getElementById('cc-pac-dropdown');
        const hidden   = document.getElementById('cc-pac-id');
        if (!input || !dropdown || !hidden) return;

        let _timer = null;
        let _idx   = -1;

        input.addEventListener('input', function () {
            const q = this.value.trim();
            hidden.value = '';
            _eliminarChip();
            clearTimeout(_timer);

            if (q.length < 2) {
                _cerrarDropdown();
                return;
            }

            dropdown.innerHTML = '<div class="cc-autocomplete-loading"><i class="ti ti-loader-2 me-1"></i>Buscando...</div>';
            dropdown.classList.add('open');

            _timer = setTimeout(() => {
                fetch(`/pacientes/buscar?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(items => _renderDropdown(items, q))
                    .catch(() => {
                        dropdown.innerHTML = '<div class="cc-autocomplete-empty text-danger">Error en la búsqueda.</div>';
                    });
            }, 280);
        });

        // Navegación teclado
        input.addEventListener('keydown', function (e) {
            const items = dropdown.querySelectorAll('.cc-autocomplete-item');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                _idx = Math.min(_idx + 1, items.length - 1);
                _resaltarItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                _idx = Math.max(_idx - 1, 0);
                _resaltarItem(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (_idx >= 0 && items[_idx]) items[_idx].click();
            } else if (e.key === 'Escape') {
                _cerrarDropdown();
            }
        });

        // Cerrar al hacer click fuera
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#cc-pac-wrap')) _cerrarDropdown();
        });

        function _resaltarItem(items) {
            items.forEach((el, i) => el.classList.toggle('active', i === _idx));
            if (items[_idx]) items[_idx].scrollIntoView({ block: 'nearest' });
        }

        function _renderDropdown(items, q) {
            _idx = -1;
            if (!items.length) {
                dropdown.innerHTML = '<div class="cc-autocomplete-empty">Sin resultados para "' + _esc(q) + '"</div>';
                return;
            }
            dropdown.innerHTML = items.map(p => `
                <div class="cc-autocomplete-item" data-id="${p.id}" data-nombre="${_esc(p.nombre)}">
                    <div class="cc-autocomplete-name">${_highlight(p.nombre, q)}</div>
                    <div class="cc-autocomplete-sub">
                        ${p.telefono ? '<i class="ti ti-phone" style="font-size:.7rem"></i> ' + _esc(p.telefono) : ''}
                        ${p.email    ? ' &nbsp;<i class="ti ti-mail" style="font-size:.7rem"></i> ' + _esc(p.email) : ''}
                    </div>
                </div>`).join('');

            dropdown.querySelectorAll('.cc-autocomplete-item').forEach(el => {
                el.addEventListener('click', () => {
                    hidden.value  = el.dataset.id;
                    input.value   = '';
                    input.style.display = 'none';
                    _mostrarChip(el.dataset.nombre);
                    _cerrarDropdown();
                });
            });
        }

        function _mostrarChip(nombre) {
            _eliminarChip();
            const chip = document.createElement('div');
            chip.className  = 'cc-pac-chip';
            chip.id         = 'cc-pac-chip';
            chip.innerHTML  = `<i class="ti ti-user" style="font-size:.8rem"></i> ${_esc(nombre)} <span class="cc-pac-chip-clear" id="cc-pac-chip-clear">×</span>`;
            input.parentNode.insertBefore(chip, input.nextSibling);
            document.getElementById('cc-pac-chip-clear').addEventListener('click', () => {
                hidden.value = '';
                _eliminarChip();
                input.style.display = '';
                input.focus();
            });
        }

        function _eliminarChip() {
            const chip = document.getElementById('cc-pac-chip');
            if (chip) chip.remove();
        }

        function _cerrarDropdown() {
            dropdown.classList.remove('open');
            dropdown.innerHTML = '';
            _idx = -1;
        }
    }

    function _highlight(texto, q) {
        const re = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return _esc(texto).replace(re, '<mark style="background:rgba(102,126,234,.18);border-radius:3px;padding:0 2px">$1</mark>');
    }

    function _esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Arranque ──────────────────────────────────────────────────────────────
    function init() {
        const sel = document.getElementById('cc-especialista');
        if (!sel) return;

        _initAutocompletePac();

        // Soporta tanto Select2 (jQuery event) como select nativo
        function _onEspChange() {
            _espId = sel.value || null;
            _reset();
            if (_espId) {
                document.getElementById('cc-bloque-calendario').style.display = '';
                _cargarMes();
            } else {
                document.getElementById('cc-bloque-calendario').style.display = 'none';
            }
        }
        sel.addEventListener('change', _onEspChange);
        // Select2 dispara 'change' sobre el elemento nativo también,
        // así que un solo listener es suficiente.

        // Cuando se abre el modal, resetear
        const modal = document.getElementById('modal-crear-cita');
        if (modal) {
            modal.addEventListener('show.bs.modal', _resetModal);
        }
    }

    function _reset() {
        _fechaSel = null;
        _slotSel  = null;
        _mesActual = new Date();
        _mesActual.setDate(1);
        _disponib = {};

        document.getElementById('cc-bloque-slots').style.display    = 'none';
        document.getElementById('cc-bloque-paciente').style.display  = 'none';
        document.getElementById('cc-bloque-servicio').style.display  = 'none';
        document.getElementById('cc-bloque-lead1').style.display     = 'none';
        document.getElementById('cc-bloque-lead2').style.display     = 'none';
        document.getElementById('cc-bloque-estatus').style.display   = 'none';
        document.getElementById('cc-bloque-origen').style.display    = 'none';
        document.getElementById('cc-bloque-motivo').style.display    = 'none';
        document.getElementById('cc-bloque-obs').style.display       = 'none';
        document.getElementById('cc-btn-guardar').style.display      = 'none';
        document.getElementById('cc-fecha-hidden').value             = '';
        document.getElementById('cc-hora-inicio-hidden').value       = '';
        document.getElementById('cc-hora-fin-hidden').value          = '';
    }

    function _resetModal() {
        _espId = null;
        _reset();
        const sel = document.getElementById('cc-especialista');
        if (sel) sel.value = '';
        // Reset Select2
        if (typeof $ !== 'undefined' && $('#cc-especialista').data('select2')) {
            $('#cc-especialista').val('').trigger('change.select2');
        }
        document.getElementById('cc-bloque-calendario').style.display = 'none';
        document.getElementById('cc-calendario').innerHTML = '';
        // Limpiar autocomplete paciente
        const pacInput = document.getElementById('cc-pac-input');
        const pacId    = document.getElementById('cc-pac-id');
        const pacChip  = document.getElementById('cc-pac-chip');
        if (pacInput) { pacInput.value = ''; pacInput.style.display = ''; }
        if (pacId)    pacId.value = '';
        if (pacChip)  pacChip.remove();
    }

    // ── Carga disponibilidad del mes ──────────────────────────────────────────
    function _cargarMes() {
        if (!_espId || _cargandoCal) return;
        _cargandoCal = true;

        const mes = _mesActual.getFullYear() + '-' +
                    String(_mesActual.getMonth() + 1).padStart(2, '0');

        document.getElementById('cc-calendario').innerHTML =
            '<div class="cc-cal-loading"><i class="ti ti-loader-2 me-1"></i>Cargando...</div>';

        fetch(`/citas/disponibilidad/${_espId}?mes=${mes}`)
            .then(r => r.json())
            .then(data => {
                _disponib = data;
                _renderCal();
            })
            .catch(() => {
                document.getElementById('cc-calendario').innerHTML =
                    '<div class="cc-cal-loading text-danger">Error al cargar disponibilidad.</div>';
            })
            .finally(() => { _cargandoCal = false; });
    }

    // ── Renderizar el mini calendario ─────────────────────────────────────────
    function _renderCal() {
        const year  = _mesActual.getFullYear();
        const month = _mesActual.getMonth();

        const titulo = MESES_ES[month] + ' ' + year;

        // Primer día del mes → qué columna ocupa (lun=0..dom=6)
        const primerDia = new Date(year, month, 1);
        const startCol  = (primerDia.getDay() + 6) % 7; // 0=lun

        const diasEnMes = new Date(year, month + 1, 0).getDate();
        const hoy       = new Date();
        const hoyStr    = `${hoy.getFullYear()}-${String(hoy.getMonth()+1).padStart(2,'0')}-${String(hoy.getDate()).padStart(2,'0')}`;

        let html = `
        <div class="cc-cal-header">
            <button class="cc-cal-nav" id="cc-cal-prev" type="button">‹</button>
            <span class="cc-cal-title">${titulo}</span>
            <button class="cc-cal-nav" id="cc-cal-next" type="button">›</button>
        </div>
        <div class="cc-cal-dow">
            <span>Lu</span><span>Ma</span><span>Mi</span>
            <span>Ju</span><span>Vi</span><span>Sá</span><span>Do</span>
        </div>
        <div class="cc-cal-days">`;

        // Celdas vacías al inicio
        for (let i = 0; i < startCol; i++) {
            html += `<div class="cc-cal-day other-month"></div>`;
        }

        for (let d = 1; d <= diasEnMes; d++) {
            const fechaStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const estado   = _disponib[fechaStr] || 'sin_horario';
            const esHoy    = fechaStr === hoyStr;
            const esSel    = fechaStr === _fechaSel;
            const esPasado = new Date(year, month, d) < new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());

            let cls = 'cc-cal-day';
            if (esPasado)              cls += ' sin-horario';
            else if (estado === 'sin_horario') cls += ' sin-horario';
            else if (estado === 'lleno')       cls += ' lleno';
            else                               cls += ' disponible';
            if (esHoy)  cls += ' hoy';
            if (esSel)  cls += ' seleccionado';

            html += `<div class="${cls}" data-fecha="${fechaStr}">${d}</div>`;
        }

        html += `</div>
        <div class="cc-leyenda" style="padding:6px 12px 8px">
            <span><i class="dot dot-disponible"></i>Disponible</span>
            <span><i class="dot dot-lleno"></i>Lleno</span>
            <span><i class="dot dot-sin"></i>Sin horario</span>
        </div>`;

        document.getElementById('cc-calendario').innerHTML = html;

        // Navegación entre meses
        document.getElementById('cc-cal-prev').addEventListener('click', () => {
            _mesActual.setMonth(_mesActual.getMonth() - 1);
            _cargarMes();
        });
        document.getElementById('cc-cal-next').addEventListener('click', () => {
            _mesActual.setMonth(_mesActual.getMonth() + 1);
            _cargarMes();
        });

        // Click en día disponible
        document.querySelectorAll('#cc-calendario .cc-cal-day.disponible').forEach(el => {
            el.addEventListener('click', () => _seleccionarFecha(el.dataset.fecha));
        });
    }

    // ── Seleccionar fecha ─────────────────────────────────────────────────────
    function _seleccionarFecha(fecha) {
        _fechaSel = fecha;
        _slotSel  = null;
        document.getElementById('cc-fecha-hidden').value     = fecha;
        document.getElementById('cc-hora-inicio-hidden').value = '';
        document.getElementById('cc-hora-fin-hidden').value    = '';

        // Remarca el día seleccionado
        document.querySelectorAll('#cc-calendario .cc-cal-day').forEach(el => {
            el.classList.remove('seleccionado');
            if (el.dataset.fecha === fecha) el.classList.add('seleccionado');
        });

        // Muestra fecha legible
        const [y, m, d] = fecha.split('-');
        const dt = new Date(parseInt(y), parseInt(m)-1, parseInt(d));
        document.getElementById('cc-fecha-legible').textContent =
            DIAS_ES[dt.getDay()] + ' ' + parseInt(d) + ' de ' + MESES_ES[dt.getMonth()] + ' ' + y;

        // Ocultar campos hasta elegir slot
        document.getElementById('cc-bloque-slots').style.display = '';
        _ocultarCamposExtra();
        _cargarSlots(fecha);
    }

    function _ocultarCamposExtra() {
        ['cc-bloque-paciente','cc-bloque-servicio','cc-bloque-lead1','cc-bloque-lead2',
         'cc-bloque-estatus','cc-bloque-origen','cc-bloque-motivo','cc-bloque-obs',
         'cc-btn-guardar'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
    }

    // ── Cargar slots de hora ──────────────────────────────────────────────────
    function _cargarSlots(fecha) {
        if (_cargandoSlot) return;
        _cargandoSlot = true;

        document.getElementById('cc-slots').innerHTML =
            '<div class="cc-slots-loading"><i class="ti ti-loader-2 me-1"></i>Cargando horas...</div>';

        fetch(`/citas/horas-disponibles/${_espId}?fecha=${fecha}`)
            .then(r => r.json())
            .then(slots => _renderSlots(slots))
            .catch(() => {
                document.getElementById('cc-slots').innerHTML =
                    '<div class="cc-slots-loading text-danger">Error al cargar horas.</div>';
            })
            .finally(() => { _cargandoSlot = false; });
    }

    function _renderSlots(slots) {
        if (!slots.length) {
            document.getElementById('cc-slots').innerHTML =
                '<div class="cc-slots-loading text-muted">No hay horario configurado para este día.</div>';
            return;
        }

        const html = slots.map(s => {
            const cls = s.disponible ? 'cc-slot' : 'cc-slot ocupado';
            return `<button type="button" class="${cls}"
                        data-hi="${s.hora_inicio}" data-hf="${s.hora_fin}">
                        ${s.hora_inicio} – ${s.hora_fin}
                    </button>`;
        }).join('');

        document.getElementById('cc-slots').innerHTML = html;

        document.querySelectorAll('#cc-slots .cc-slot:not(.ocupado)').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('#cc-slots .cc-slot').forEach(b => b.classList.remove('seleccionado'));
                btn.classList.add('seleccionado');
                _slotSel = { hi: btn.dataset.hi, hf: btn.dataset.hf };
                document.getElementById('cc-hora-inicio-hidden').value = btn.dataset.hi;
                document.getElementById('cc-hora-fin-hidden').value    = btn.dataset.hf;
                _mostrarCamposExtra();
            });
        });
    }

    function _mostrarCamposExtra() {
        ['cc-bloque-paciente','cc-bloque-servicio','cc-bloque-lead1','cc-bloque-lead2',
         'cc-bloque-estatus','cc-bloque-origen','cc-bloque-motivo','cc-bloque-obs'].forEach(id => {
            document.getElementById(id).style.display = '';
        });
        document.getElementById('cc-btn-guardar').style.display = '';
    }

    return { init };
})();
