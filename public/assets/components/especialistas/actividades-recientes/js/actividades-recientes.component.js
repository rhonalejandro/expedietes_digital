/**
 * EspActividadesRecientes
 * Componente independiente de paginación infinita para el log de especialistas.
 * Uso: EspActividadesRecientes.init({ especialistaId, endpoint, csrfToken })
 */
const EspActividadesRecientes = (function () {

    const ICONOS = {
        creacion     : { icon: 'ti-user-plus',   cls: 'creacion',    label: 'Especialista registrado' },
        edicion      : { icon: 'ti-edit',         cls: 'edicion',     label: 'Datos editados'          },
        eliminacion  : { icon: 'ti-trash',        cls: 'eliminacion', label: 'Registro eliminado'      },
        cambio_estado: { icon: 'ti-toggle-left',  cls: 'estado',      label: 'Estado modificado'       },
    };

    function _meta(detalles) {
        return [
            `<span><i class="ti ti-user"></i>${detalles.usuario || '—'}</span>`,
            `<span><i class="ti ti-calendar"></i>${detalles.fecha || '—'}</span>`,
            `<span><i class="ti ti-clock"></i>${detalles.hora || '—'}</span>`,
            `<span><i class="ti ti-map-pin"></i>${detalles.ip || '—'}</span>`,
        ].join('');
    }

    function _labelCampo(campo) {
        const labels = {
            nombre        : 'Nombre',
            apellido      : 'Apellido',
            tratamiento   : 'Tratamiento',
            profesion     : 'Profesión',
            especialidad  : 'Especialidad',
            num_colegiado : 'N.º Colegiado',
            telefono      : 'Teléfono',
            email         : 'Correo',
            firma         : 'Firma',
            estado        : 'Estado',
        };
        return labels[campo] || campo;
    }

    function _val(v) {
        if (v === null || v === undefined || v === '') {
            return '<span class="val-null">vacío</span>';
        }
        return v;
    }

    function _cambiosHTML(cambios) {
        if (!cambios || !Object.keys(cambios).length) return '';
        let rows = '';
        for (const [campo, vals] of Object.entries(cambios)) {
            rows += `
            <div class="act-cambios__row">
                <span>${_labelCampo(campo)}</span>
                <span class="val-anterior">${_val(vals.anterior)}</span>
                <span class="val-actual">${_val(vals.actual)}</span>
            </div>`;
        }
        return `
        <div class="act-cambios mt-2">
            <div class="act-cambios__head">
                <span>Campo</span><span>Anterior</span><span>Actual</span>
            </div>
            ${rows}
        </div>`;
    }

    function _renderItem(log) {
        const cfg      = ICONOS[log.tipo_accion] || ICONOS.edicion;
        const detalles = log.detalles || {};

        let cambiosHTML = '';
        if (log.tipo_accion === 'creacion' && detalles.datos_iniciales) {
            const ini = {};
            for (const [k, v] of Object.entries(detalles.datos_iniciales)) {
                ini[k] = { anterior: null, actual: v };
            }
            cambiosHTML = _cambiosHTML(ini);
        } else if (detalles.cambios) {
            cambiosHTML = _cambiosHTML(detalles.cambios);
        }

        return `
        <div class="act-item">
            <div class="act-item__icon act-item__icon--${cfg.cls}">
                <i class="ti ${cfg.icon}"></i>
            </div>
            <div class="act-item__body">
                <div class="act-item__title">${cfg.label}</div>
                <div class="act-item__meta">${_meta(detalles)}</div>
                ${cambiosHTML}
            </div>
        </div>`;
    }

    function init(cfg) {
        const { especialistaId, endpoint, csrfToken } = cfg;
        const feed     = document.getElementById(`esp-act-feed-${especialistaId}`);
        const loader   = document.getElementById(`esp-act-loader-${especialistaId}`);
        const empty    = document.getElementById(`esp-act-empty-${especialistaId}`);
        const sentinel = document.getElementById(`esp-act-sentinel-${especialistaId}`);

        let page      = 1;
        let loading   = false;
        let exhausted = false;

        function fetchPage() {
            if (loading || exhausted) return;
            loading = true;
            loader.classList.remove('d-none');

            fetch(`${endpoint}?page=${page}`, {
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                const items = data.data || [];
                items.forEach(log => {
                    feed.insertAdjacentHTML('beforeend', _renderItem(log));
                });

                if (page === 1 && items.length === 0) {
                    empty.classList.remove('d-none');
                }

                if (!data.next_page_url) {
                    exhausted = true;
                    observer.disconnect();
                }

                page++;
            })
            .catch(() => { exhausted = true; })
            .finally(() => {
                loading = false;
                loader.classList.add('d-none');
            });
        }

        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) fetchPage();
        }, { threshold: 0.1 });

        observer.observe(sentinel);
    }

    return { init };
})();
