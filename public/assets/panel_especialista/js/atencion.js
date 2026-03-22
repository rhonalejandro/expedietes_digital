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

    // ── Selector de Zonas Podológicas ─────────────────────────────────────

    var ZONA_LABELS = {
        talon:  'Talón',    planta: 'Planta',  arco:   'Arco',  antepi: 'Antepié',
        dedo_1: 'Dedo 1',  dedo_2: 'Dedo 2',  dedo_3: 'Dedo 3',
        dedo_4: 'Dedo 4',  dedo_5: 'Dedo 5',
        'uña_1': 'Uña D1', 'uña_2': 'Uña D2', 'uña_3': 'Uña D3',
        'uña_4': 'Uña D4', 'uña_5': 'Uña D5',
    };

    // Estado: { izquierdo: Set, derecho: Set }
    var zonas = { izquierdo: new Set(), derecho: new Set() };
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
            chip.dataset.zona = zona;
            chip.dataset.pie  = pie;
            chip.innerHTML = (ZONA_LABELS[zona] || zona) + ' <i class="ti ti-x"></i>';
            chip.addEventListener('click', function () {
                desactivarZona(pie, zona);
            });
            chipsEl.appendChild(chip);
        });
    }

    function activarZona(pie, zona, pathEl) {
        zonas[pie].add(zona);
        if (pathEl) pathEl.classList.add('zona-activa');
        renderChips(pie);
        serializarZonas();
    }

    function desactivarZona(pie, zona) {
        zonas[pie].delete(zona);
        // Quitar clase visual de todos los elementos con ese zona+pie
        document.querySelectorAll('.foot-svg[data-pie="' + pie + '"] .zona-path[data-zona="' + zona + '"]')
            .forEach(function (el) { el.classList.remove('zona-activa'); });
        renderChips(pie);
        serializarZonas();
    }

    // Bind click en todas las zonas SVG
    document.querySelectorAll('.foot-svg').forEach(function (svg) {
        var pie = svg.dataset.pie; // 'izquierdo' | 'derecho'

        svg.querySelectorAll('.zona-path').forEach(function (path) {
            path.addEventListener('click', function (e) {
                e.stopPropagation();
                var zona = this.dataset.zona;
                if (zonas[pie].has(zona)) {
                    desactivarZona(pie, zona);
                } else {
                    activarZona(pie, zona, this);
                }
            });

            // Tooltip nativo del título SVG ya lo maneja el browser
        });
    });

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
