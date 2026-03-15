/* ================================================================
   LogActividad — Componente unificado de historial de actividad
   Uso: LogActividad.init({ uid, modulo, registroId, endpoint, csrf })
   Soporta ambos formatos de cambios:
     - Nuevo (LogSistemaHelper): array de { campo, etiqueta, valor_anterior, valor_actual }
     - Legado: objeto { campo: { anterior, actual } }
   ================================================================ */

const LogActividad = (function () {

    'use strict';

    // ── Iconos y etiquetas por tipo de acción ─────────────────────────────────
    const ACCIONES = {
        creado:           { icon: 'ti-user-plus',    cls: 'creacion',   label: 'registró'  },
        creada:           { icon: 'ti-calendar-plus', cls: 'creacion',  label: 'registró'  },
        editado:          { icon: 'ti-edit',          cls: 'edicion',   label: 'editó'     },
        editada:          { icon: 'ti-edit',          cls: 'edicion',   label: 'editó'     },
        eliminado:        { icon: 'ti-trash',         cls: 'eliminacion', label: 'eliminó' },
        eliminada:        { icon: 'ti-trash',         cls: 'eliminacion', label: 'eliminó' },
        estado_cambiado:  { icon: 'ti-toggle-left',   cls: 'estado',    label: 'cambió el estado de' },
        estatus_cambiado: { icon: 'ti-flag',          cls: 'estatus',   label: 'cambió el estatus de' },
        // Fallback para acciones no mapeadas
        _default:         { icon: 'ti-activity',      cls: 'edicion',   label: 'realizó acción' },
    };

    // ── Helpers ───────────────────────────────────────────────────────────────
    function _esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function _val(v) {
        if (v === null || v === undefined || v === '' || v === '—') {
            return '<span class="la-val-null">(vacío)</span>';
        }
        return _esc(String(v));
    }

    /**
     * Normaliza el campo `cambios` del detalles a un array uniforme.
     * Soporta 3 formatos:
     *   1. Nuevo (LogSistemaHelper): array [{ campo, etiqueta, valor_anterior, valor_actual }]
     *   2. Legado dict:              { campo: { anterior, actual } }
     *   3. Legado array sin etiqueta:[{ campo, anterior, actual }]  (por si acaso)
     */
    function _normalizarCambios(cambios) {
        if (!cambios) return [];

        // ── Formato nuevo: array de objetos con 'etiqueta' ────────────────────
        if (Array.isArray(cambios)) {
            return cambios
                .filter(c => c && typeof c === 'object' && ('campo' in c || 'etiqueta' in c))
                .map(c => ({
                    campo:          c.campo          ?? '?',
                    etiqueta:       c.etiqueta        || c.campo || '?',
                    valor_anterior: c.valor_anterior  ?? c.anterior,
                    valor_actual:   c.valor_actual    ?? c.actual,
                }));
        }

        // ── Formato legado: objeto { campo: { anterior, actual } } ────────────
        if (typeof cambios === 'object') {
            return Object.entries(cambios).map(([campo, vals]) => ({
                campo,
                etiqueta:       _etiquetaLegado(campo),
                valor_anterior: vals?.anterior,
                valor_actual:   vals?.actual,
            }));
        }

        return [];
    }

    /**
     * Convierte nombres de campo a etiquetas legibles (compatibilidad legado).
     * El nuevo formato ya trae `etiqueta` del servidor.
     */
    function _etiquetaLegado(campo) {
        const MAP = {
            nombre: 'Nombre', apellido: 'Apellido',
            identificacion: 'Cédula / Identificación',
            tipo_identificacion: 'Tipo de Identificación',
            fecha_nacimiento: 'Fecha de Nacimiento',
            email: 'Correo Electrónico', contacto: 'Teléfono / Contacto',
            direccion: 'Dirección', genero: 'Género', estado: 'Estado',
            ocupacion: 'Ocupación', nacionalidad: 'Nacionalidad',
            seguro_medico: 'Seguro Médico', contacto_emergencia: 'Contacto de Emergencia',
            tratamiento: 'Tratamiento / Título', profesion: 'Profesión',
            especialidad: 'Especialidad', num_colegiado: 'N° de Colegiado',
            telefono: 'Teléfono', firma: 'Firma Digital',
            especialista_id: 'Especialista', paciente_id: 'Paciente',
            sucursal_id: 'Sucursal', servicio_id: 'Servicio',
            fecha: 'Fecha de la Cita', hora_inicio: 'Hora de Inicio',
            hora_fin: 'Hora de Fin', estatus: 'Estatus',
            motivo: 'Motivo de Consulta', observaciones: 'Observaciones',
            origen: 'Origen de la Cita',
        };
        return MAP[campo] || campo;
    }

    // ── Construcción del HTML de cada item ────────────────────────────────────

    function _buildTituloEdicion(usuario, accionLabel, cambios) {
        const listaEtiquetas = cambios.map(c => c.etiqueta).join(', ');
        const n              = cambios.length;
        return `<strong>${_esc(usuario)}</strong> ${_esc(accionLabel)} `
             + `<strong>${n}</strong> campo${n !== 1 ? 's' : ''}: `
             + `<span class="la-campos-lista">${_esc(listaEtiquetas)}</span>`;
    }

    function _buildCardEdicion(cambios) {
        const n = cambios.length;
        const filas = cambios.map(c => `
            <tr>
                <td class="la-td-etiqueta">${_esc(c.etiqueta)}</td>
                <td class="la-td-anterior">${_val(c.valor_anterior)}</td>
                <td class="la-td-flecha">→</td>
                <td class="la-td-actual">${_val(c.valor_actual)}</td>
            </tr>`).join('');

        return `
        <div class="la-card">
            <div class="la-card-subtitulo">${n} campo${n !== 1 ? 's' : ''} modificado${n !== 1 ? 's' : ''}</div>
            <table class="la-cambios-table">
                <tbody>${filas}</tbody>
            </table>
        </div>`;
    }

    function _buildCardCreacion(detalles) {
        const campos = detalles.campos || [];
        if (!campos.length) return '';

        const chips = campos
            .filter(c => c.valor_actual && c.valor_actual !== '—')
            .map(c => `<span class="la-campo-inicial">${_esc(c.etiqueta)}: ${_esc(String(c.valor_actual))}</span>`)
            .join('');

        if (!chips) return '';

        return `
        <div class="la-card">
            <div class="la-card-subtitulo">${campos.length} campo${campos.length !== 1 ? 's' : ''} registrado${campos.length !== 1 ? 's' : ''}</div>
            <div class="la-campos-creacion">${chips}</div>
        </div>`;
    }

    function _buildCardEliminacion(detalles) {
        const nombre = detalles.nombre_completo || detalles.descripcion || '—';
        return `
        <div class="la-card">
            <div class="la-eliminacion-nombre">
                Registro eliminado: <strong>${_esc(nombre)}</strong>
            </div>
        </div>`;
    }

    function _buildItem(log) {
        const accion   = log.tipo_accion || 'editado';
        const cfg      = ACCIONES[accion] || ACCIONES._default;
        const detalles = log.detalles    || {};
        const usuario  = _esc(log.usuario || 'Sistema');
        const fecha    = _esc(log.fecha   || '');
        const ip       = _esc(log.ip      || '—');

        const cambios  = _normalizarCambios(detalles.cambios);
        const esEdicion = ['editado','editada','estado_cambiado','estatus_cambiado'].includes(accion)
                       || (detalles.tipo === 'edicion');
        const esCreacion = ['creado','creada'].includes(accion) || detalles.tipo === 'creacion';
        const esElim     = ['eliminado','eliminada'].includes(accion);

        // ── Título de la cabecera ─────────────────────────────────────────────
        let tituloHTML;
        if (esEdicion && cambios.length) {
            tituloHTML = _buildTituloEdicion(usuario, cfg.label, cambios);
        } else if (esCreacion) {
            tituloHTML = `<strong>${usuario}</strong> registró un nuevo registro`;
        } else if (esElim) {
            tituloHTML = `<strong>${usuario}</strong> eliminó el registro`;
        } else {
            tituloHTML = `<strong>${usuario}</strong> ${_esc(cfg.label)}`;
        }

        // ── Tarjeta de detalle ────────────────────────────────────────────────
        let cardHTML = '';
        if (esEdicion && cambios.length) {
            cardHTML = _buildCardEdicion(cambios);
        } else if (esCreacion) {
            cardHTML = _buildCardCreacion(detalles);
        } else if (esElim) {
            cardHTML = _buildCardEliminacion(detalles);
        }

        return `
<div class="la-item">
    <div class="la-bullet la-bullet--${cfg.cls}">
        <i class="ti ${cfg.icon}"></i>
    </div>
    <div class="la-item-body">
        <div class="la-item-head">
            <div class="la-item-titulo">${tituloHTML}</div>
            <div class="la-item-fecha">${fecha}</div>
        </div>
        ${cardHTML}
        <div class="la-item-footer">
            <span class="la-footer-usuario"><i class="ti ti-user"></i>${usuario}</span>
            <span class="la-footer-ip"><i class="ti ti-map-pin"></i>${ip}</span>
        </div>
    </div>
</div>`;
    }

    // ── Registro de instancias activas (para re-inicialización en modales) ──────
    const _instancias = {};

    // ── Init / Re-init ────────────────────────────────────────────────────────

    /**
     * Inicializa (o re-inicializa) el componente para un uid dado.
     * Si ya existe una instancia con ese uid, la destruye y arranca limpia.
     * Útil en modales que se reusan con diferente registroId.
     */
    function init(cfg) {
        const { uid, endpoint, csrf } = cfg;

        // Destruir instancia previa si existe
        if (_instancias[uid]) {
            _instancias[uid].destroy();
            delete _instancias[uid];
        }

        const feed     = document.getElementById(`${uid}-feed`);
        const sentinel = document.getElementById(`${uid}-sentinel`);
        const spinner  = document.getElementById(`${uid}-spinner`);
        const empty    = document.getElementById(`${uid}-empty`);
        const badge    = document.getElementById(`${uid}-badge`);

        if (!feed || !sentinel) return;

        // Limpiar DOM previo
        feed.innerHTML     = '';
        sentinel.innerHTML = '';
        if (empty) empty.style.display = 'none';
        if (badge) badge.textContent   = '—';

        let page      = 1;
        let loading   = false;
        let exhausted = false;

        function _fetchPage() {
            if (loading || exhausted) return;
            loading = true;
            if (spinner) spinner.style.display = 'inline-block';

            fetch(`${endpoint}?page=${page}`, {
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(json => {
                const items = json.data || [];

                items.forEach(log => {
                    feed.insertAdjacentHTML('beforeend', _buildItem(log));
                });

                if (badge && json.total !== undefined) {
                    badge.textContent = json.total;
                }

                if (page === 1 && items.length === 0 && empty) {
                    empty.style.display = '';
                }

                if (!json.next_page_url) {
                    exhausted = true;
                    observer.disconnect();
                    if (json.total > 0 && sentinel) {
                        sentinel.innerHTML = '<span class="la-fin-texto">— Fin del historial —</span>';
                    }
                }

                page++;
            })
            .catch(err => {
                console.error('[LogActividad] Error:', err);
                exhausted = true;
            })
            .finally(() => {
                loading = false;
                if (spinner) spinner.style.display = 'none';
            });
        }

        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) _fetchPage();
        }, { rootMargin: '80px' });

        observer.observe(sentinel);

        // Guardar referencia para poder destruirla
        _instancias[uid] = {
            destroy() { observer.disconnect(); }
        };
    }

    return { init };

})();
