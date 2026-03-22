{{--
  Selector de Zona Podológica — hotspots sobre imágenes PNG reales
  Posiciones calibradas manualmente (modo calibración).
--}}

<input type="hidden" name="zonas_afectadas" id="zonas-json-input" value="">

<div class="atc-zonas-container" id="zonas-container">

    {{-- ══════════════════════ PIE DERECHO ══════════════════════ --}}
    <div class="atc-pie-panel">
        <div class="atc-pie-titulo">
            Pie Derecho <i class="ti ti-arrow-right"></i>
        </div>

        <div class="pie-img-wrapper" data-pie="derecho">
            <img src="{{ asset('assets/Pies/pie-derecho.png') }}"
                 alt="Pie Derecho — vista plantar y dorsal"
                 class="pie-img">

            {{-- Vista plantar (mitad izquierda) --}}
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_1" data-label="Hallux"
                    style="top:8.8%;left:25.7%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_2" data-label="Índice"
                    style="top:9.6%;left:17.6%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_3" data-label="Medio"
                    style="top:13%;left:12.2%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_4" data-label="Anular"
                    style="top:18.4%;left:7.5%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_5" data-label="Meñique"
                    style="top:25.7%;left:4.1%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="antepi" data-label="Antepié"
                    style="top:33%;left:18.6%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="planta" data-label="Planta"
                    style="top:61%;left:24.4%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="arco" data-label="Arco plantar"
                    style="top:51.4%;left:11.2%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="talon" data-label="Talón"
                    style="top:87%;left:19%"></button>

            {{-- Vista dorsal (mitad derecha) --}}
            <button type="button" class="zona-hotspot"
                    data-zona="tobillo" data-label="Tobillo"
                    style="top:5.9%;left:73.2%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="dorso" data-label="Dorso del pie"
                    style="top:41%;left:76.8%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_1" data-label="Uña Hallux"
                    style="top:95.1%;left:90.6%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_2" data-label="Uña Índice"
                    style="top:94.4%;left:81.1%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_3" data-label="Uña Medio"
                    style="top:89%;left:76.1%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_4" data-label="Uña Anular"
                    style="top:83.6%;left:71.4%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_5" data-label="Uña Meñique"
                    style="top:77.1%;left:67.6%"></button>
        </div>

        <div class="atc-zonas-chips" id="chips-derecho">
            <span class="atc-chips-empty">Sin zonas seleccionadas</span>
        </div>
    </div>

    {{-- ══════════════════════ PIE IZQUIERDO ══════════════════════ --}}
    <div class="atc-pie-panel">
        <div class="atc-pie-titulo">
            <i class="ti ti-arrow-left"></i> Pie Izquierdo
        </div>

        <div class="pie-img-wrapper" data-pie="izquierdo">
            <img src="{{ asset('assets/Pies/pie-izquierdo.png') }}"
                 alt="Pie Izquierdo — vista dorsal y plantar"
                 class="pie-img">

            {{-- Vista dorsal (mitad izquierda) --}}
            <button type="button" class="zona-hotspot"
                    data-zona="tobillo" data-label="Tobillo"
                    style="top:8.4%;left:26.6%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="dorso" data-label="Dorso del pie"
                    style="top:41.6%;left:21%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_5" data-label="Uña Meñique"
                    style="top:76.4%;left:33.2%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_4" data-label="Uña Anular"
                    style="top:93.8%;left:18.8%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_3" data-label="Uña Medio"
                    style="top:89.1%;left:23.9%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_2" data-label="Uña Índice"
                    style="top:84.7%;left:29.5%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="uña_1" data-label="Uña Hallux"
                    style="top:94.7%;left:9.3%"></button>

            {{-- Vista plantar (mitad derecha) --}}
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_5" data-label="Meñique"
                    style="top:25.5%;left:96%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_4" data-label="Anular"
                    style="top:18.6%;left:92.6%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_3" data-label="Medio"
                    style="top:13.6%;left:88.2%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_2" data-label="Índice"
                    style="top:9.5%;left:82.6%"></button>
            <button type="button" class="zona-hotspot hotspot-sm"
                    data-zona="dedo_1" data-label="Hallux"
                    style="top:7%;left:75.5%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="antepi" data-label="Antepié"
                    style="top:33.8%;left:80.5%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="planta" data-label="Planta"
                    style="top:61%;left:74.7%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="arco" data-label="Arco plantar"
                    style="top:52.2%;left:91.3%"></button>
            <button type="button" class="zona-hotspot"
                    data-zona="talon" data-label="Talón"
                    style="top:87%;left:79%"></button>
        </div>

        <div class="atc-zonas-chips" id="chips-izquierdo">
            <span class="atc-chips-empty">Sin zonas seleccionadas</span>
        </div>
    </div>

</div>

{{-- Barra de calibración — solo visible si AJUSTE_MAPA_PIE=true en .env --}}
@if(env('AJUSTE_MAPA_PIE', false))
<div class="d-flex align-items-center gap-2 mt-2 mb-1">
    <button type="button" id="btn-calibrar"
            class="btn btn-sm btn-calibrar-trigger">
        <i class="ti ti-adjustments-horizontal"></i> Ajustar posiciones
    </button>
    <button type="button" id="btn-log-zonas"
            class="btn btn-sm btn-calibrar-trigger" style="display:none">
        <i class="ti ti-terminal"></i> Copiar al log
    </button>
    <span id="calibrar-hint" class="text-muted" style="font-size:.72rem;display:none">
        Arrastrá los puntos. Abrí consola (F12) para ver coordenadas.
    </span>
</div>
@endif

{{-- Leyenda --}}
<div class="atc-zonas-legend">
    <div class="atc-legend-item">
        <span class="atc-legend-dot" style="background:rgb(var(--primary))"></span>
        Zona seleccionada
    </div>
    <div class="atc-legend-item">
        <i class="ti ti-hand-click" style="font-size:.85rem;color:#94a3b8"></i>
        Clic para seleccionar / deseleccionar
    </div>
</div>
