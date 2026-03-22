(function () {
    'use strict';

    // ── Toggle caso existente / nuevo ─────────────────────────────────────
    var optExistente = document.getElementById('opt-existente');
    var optNuevo     = document.getElementById('opt-nuevo');
    var nuevoFields  = document.getElementById('nuevo-caso-fields');
    var casoIdInput  = document.getElementById('caso-id-input');

    function activarOpcion(activa, inactiva) {
        activa.classList.add('atc-caso-opt--active');
        if (inactiva) inactiva.classList.remove('atc-caso-opt--active');
    }

    if (optExistente) {
        optExistente.addEventListener('click', function () {
            activarOpcion(optExistente, optNuevo);
            if (nuevoFields) nuevoFields.classList.add('d-none');
            if (casoIdInput) casoIdInput.disabled = false;
        });
    }

    if (optNuevo) {
        optNuevo.addEventListener('click', function () {
            activarOpcion(optNuevo, optExistente);
            if (nuevoFields) nuevoFields.classList.remove('d-none');
            if (casoIdInput) casoIdInput.disabled = true;
        });
    }

    // ── Preview de fotos ──────────────────────────────────────────────────
    var fotosInput  = document.getElementById('fotos-input');
    var fotosGrid   = document.getElementById('fotos-preview');
    var dropZone    = document.getElementById('fotos-drop-zone');
    var fileList    = []; // DataTransfer simulado para acumular archivos

    function renderPreviews() {
        fotosGrid.innerHTML = '';
        fileList.forEach(function (file, idx) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var card = document.createElement('div');
                card.className = 'atc-foto-preview';
                card.innerHTML =
                    '<img src="' + e.target.result + '" alt="foto">' +
                    '<button type="button" class="atc-foto-remove" data-idx="' + idx + '">' +
                        '<i class="ti ti-x"></i>' +
                    '</button>' +
                    '<div class="atc-foto-preview-body">' +
                        '<textarea class="atc-foto-desc" rows="2" ' +
                            'name="fotos_desc[]" placeholder="Descripción (opcional)"></textarea>' +
                    '</div>';
                fotosGrid.appendChild(card);

                card.querySelector('.atc-foto-remove').addEventListener('click', function () {
                    fileList.splice(parseInt(this.dataset.idx), 1);
                    syncInput();
                    renderPreviews();
                });
            };
            reader.readAsDataURL(file);
        });
    }

    function syncInput() {
        // Reconstruir el FileList del input usando DataTransfer
        var dt = new DataTransfer();
        fileList.forEach(function (f) { dt.items.add(f); });
        fotosInput.files = dt.files;
    }

    function addFiles(files) {
        Array.from(files).forEach(function (f) {
            if (f.type.startsWith('image/')) fileList.push(f);
        });
        syncInput();
        renderPreviews();
    }

    if (fotosInput) {
        fotosInput.addEventListener('change', function () {
            addFiles(this.files);
        });
    }

    if (dropZone) {
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });
        dropZone.addEventListener('dragleave', function () {
            dropZone.classList.remove('drag-over');
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            addFiles(e.dataTransfer.files);
        });
        dropZone.addEventListener('click', function () {
            fotosInput.click();
        });
    }

    // ── Selector de Zonas Podológicas (PNG + hotspots) ───────────────────

    var ZONA_LABELS = {
        talon:  'Talón',       planta:  'Planta',        arco:    'Arco plantar',
        antepi: 'Antepié',     tobillo: 'Tobillo',       dorso:   'Dorso',
        dedo_1: 'Hallux',      dedo_2:  'Índice',        dedo_3:  'Medio',
        dedo_4: 'Anular',      dedo_5:  'Meñique',
        'uña_1': 'Uña Hallux', 'uña_2': 'Uña Índice',   'uña_3': 'Uña Medio',
        'uña_4': 'Uña Anular', 'uña_5': 'Uña Meñique',
    };

    var zonas    = { izquierdo: new Set(), derecho: new Set() };
    var jsonInput = document.getElementById('zonas-json-input');

    function serializarZonas() {
        var obj = {};
        if (zonas.izquierdo.size) obj.izquierdo = Array.from(zonas.izquierdo);
        if (zonas.derecho.size)   obj.derecho   = Array.from(zonas.derecho);
        if (jsonInput) jsonInput.value = Object.keys(obj).length ? JSON.stringify(obj) : '';
    }

    function renderChips(pie) {
        var chipsEl = document.getElementById('chips-' + pie);
        if (!chipsEl) return;
        chipsEl.innerHTML = '';
        if (zonas[pie].size === 0) {
            chipsEl.innerHTML = '<span class="atc-chips-empty">Sin zonas seleccionadas</span>';
            return;
        }
        zonas[pie].forEach(function (zona) {
            var chip = document.createElement('span');
            chip.className = 'atc-zona-chip';
            chip.innerHTML = (ZONA_LABELS[zona] || zona) + ' <i class="ti ti-x"></i>';
            chip.addEventListener('click', function () {
                deseleccionarZona(pie, zona);
            });
            chipsEl.appendChild(chip);
        });
    }

    function seleccionarZona(pie, zona) {
        zonas[pie].add(zona);
        // Activar visualmente todos los hotspots de esa zona en ese pie
        document.querySelectorAll('[data-pie="' + pie + '"] .zona-hotspot[data-zona="' + zona + '"]')
            .forEach(function (btn) { btn.classList.add('activo'); });
        renderChips(pie);
        serializarZonas();
    }

    function deseleccionarZona(pie, zona) {
        zonas[pie].delete(zona);
        document.querySelectorAll('[data-pie="' + pie + '"] .zona-hotspot[data-zona="' + zona + '"]')
            .forEach(function (btn) { btn.classList.remove('activo'); });
        renderChips(pie);
        serializarZonas();
    }

    // Bind click en todos los hotspots
    document.querySelectorAll('.zona-hotspot').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var zona    = this.dataset.zona;
            var wrapper = this.closest('[data-pie]');
            var pie     = wrapper ? wrapper.dataset.pie : null;
            if (!pie || !zona) return;

            if (zonas[pie].has(zona)) {
                deseleccionarZona(pie, zona);
            } else {
                seleccionarZona(pie, zona);
            }
        });
    });

    // ── Modo calibración de hotspots ──────────────────────────────────────
    (function () {
        var btnCal   = document.getElementById('btn-calibrar');
        var btnLog   = document.getElementById('btn-log-zonas');
        var hint     = document.getElementById('calibrar-hint');
        var container = document.getElementById('zonas-container');
        if (!btnCal) return;

        // Badge flotante de coordenadas
        var badge = document.createElement('div');
        badge.className = 'hotspot-coord-badge';
        document.body.appendChild(badge);

        var modoActivo = false;
        var drag = null;   // hotspot que se está arrastrando
        var dragWrap = null;

        btnCal.addEventListener('click', function () {
            modoActivo = !modoActivo;
            container.classList.toggle('modo-calibracion', modoActivo);

            if (modoActivo) {
                btnCal.innerHTML  = '<i class="ti ti-check"></i> Terminar ajuste';
                btnCal.className  = 'btn btn-sm btn-warning';
                btnLog.style.display = '';
                hint.style.display   = '';
            } else {
                btnCal.innerHTML  = '<i class="ti ti-adjustments-horizontal"></i> Ajustar posiciones';
                btnCal.className  = 'btn btn-sm btn-calibrar-trigger';
                btnLog.style.display = 'none';
                hint.style.display   = 'none';
                badge.style.display  = 'none';
                logPosiciones();
            }
        });

        if (btnLog) btnLog.addEventListener('click', logPosiciones);

        // ── Drag ──────────────────────────────────────────────────────────
        document.addEventListener('mousedown', function (e) {
            if (!modoActivo) return;
            var btn = e.target.closest('.zona-hotspot');
            if (!btn) return;
            e.preventDefault();
            drag     = btn;
            dragWrap = btn.closest('.pie-img-wrapper');
            badge.style.display = 'block';
        });

        document.addEventListener('mousemove', function (e) {
            if (!drag || !dragWrap) return;
            var rect = dragWrap.getBoundingClientRect();
            var xPct = Math.max(0, Math.min(100, (e.clientX - rect.left) / rect.width  * 100));
            var yPct = Math.max(0, Math.min(100, (e.clientY - rect.top)  / rect.height * 100));
            drag.style.left = xPct.toFixed(1) + '%';
            drag.style.top  = yPct.toFixed(1) + '%';
            badge.style.left = (e.clientX + 14) + 'px';
            badge.style.top  = (e.clientY - 12) + 'px';
            badge.textContent = drag.dataset.zona
                + '  T:' + yPct.toFixed(1) + '%  L:' + xPct.toFixed(1) + '%';
        });

        document.addEventListener('mouseup', function () {
            if (!drag) return;
            drag = null;
            dragWrap = null;
        });

        // ── Log de posiciones ─────────────────────────────────────────────
        function logPosiciones() {
            var out = { izquierdo: {}, derecho: {} };
            document.querySelectorAll('.pie-img-wrapper').forEach(function (wrap) {
                var pie = wrap.dataset.pie;
                wrap.querySelectorAll('.zona-hotspot').forEach(function (btn) {
                    out[pie][btn.dataset.zona] = {
                        top:  btn.style.top,
                        left: btn.style.left
                    };
                });
            });

            console.group('%c=== POSICIONES HOTSPOTS ===', 'color:#6366f1;font-weight:bold;font-size:14px');
            console.log('%cPegá esto en el blade para guardar las posiciones:', 'color:#94a3b8');

            ['izquierdo', 'derecho'].forEach(function (pie) {
                console.group('%cPie ' + pie, 'color:#10b981;font-weight:bold');
                Object.keys(out[pie]).forEach(function (zona) {
                    var p = out[pie][zona];
                    console.log(
                        '%c' + zona + ':%c style="top:' + p.top + ';left:' + p.left + '"',
                        'color:#fbbf24;font-weight:bold', 'color:#e2e8f0'
                    );
                });
                console.groupEnd();
            });

            console.log('%cJSON completo:', 'color:#94a3b8');
            console.log(JSON.stringify(out, null, 2));
            console.groupEnd();
        }
    })();

    // ── Confirmar antes de guardar ────────────────────────────────────────
    var form      = document.getElementById('form-consulta');
    var btnGuardar = document.getElementById('btn-guardar');

    if (form) {
        form.addEventListener('submit', function (e) {
            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
            }
        });
    }

})();
