{{--
  Selector de Zona Podológica
  Vista plantar (planta del pie) con zonas clicables.
  Pie izquierdo: hallux (D1) a la DERECHA.
  Pie derecho: hallux (D1) a la IZQUIERDA (mirror del izquierdo).
--}}

<input type="hidden" name="zonas_afectadas" id="zonas-json-input" value="">

<div class="atc-zonas-container">

    {{-- PIE IZQUIERDO --}}
    <div class="atc-pie-panel">
        <div class="atc-pie-titulo">
            <i class="ti ti-arrow-left"></i> Pie Izquierdo
        </div>

        <svg class="foot-svg" viewBox="0 0 112 270" xmlns="http://www.w3.org/2000/svg"
             data-pie="izquierdo" role="img" aria-label="Diagrama pie izquierdo">

            {{-- Fondo del pie (outline decorativo, no clicable) --}}
            <path class="foot-bg-outline"
                  d="M 15 66 Q 3 90 6 120 Q 1 155 5 195 Q 5 232 22 252 Q 55 270 90 252 Q 107 232 107 195 Q 112 155 106 120 Q 109 88 105 66 Q 88 54 56 52 Q 28 51 15 66 Z"/>

            {{-- ── ZONAS DEL CUERPO ────────────────────────────── --}}

            {{-- Talón --}}
            <ellipse class="zona-path" data-zona="talon"
                     cx="55" cy="237" rx="39" ry="25">
                <title>Talón</title>
            </ellipse>

            {{-- Arco plantar (medial - lado derecho en vista plantar del pie izquierdo) --}}
            <path class="zona-path" data-zona="arco"
                  d="M 76 114 L 107 114 Q 112 155 107 200 L 80 218 Q 95 175 90 114 Z">
                <title>Arco plantar</title>
            </path>

            {{-- Planta central --}}
            <path class="zona-path" data-zona="planta"
                  d="M 6 118 Q 4 165 7 218 L 80 218 L 90 114 L 76 114 Q 55 107 32 110 Q 14 113 6 118 Z">
                <title>Planta</title>
            </path>

            {{-- Antepié (metatarsos) --}}
            <path class="zona-path" data-zona="antepi"
                  d="M 6 68 Q 3 92 6 118 Q 18 110 38 108 Q 56 106 76 114 Q 94 108 107 118 Q 110 90 105 68 Q 88 56 56 54 Q 28 53 6 68 Z">
                <title>Antepié</title>
            </path>

            {{-- ── DEDOS (D5 izquierda → D1 derecha) ──────────── --}}
            <ellipse class="zona-path" data-zona="dedo_5" cx="14" cy="42" rx="9"  ry="20"><title>Dedo 5</title></ellipse>
            <ellipse class="zona-path" data-zona="dedo_4" cx="33" cy="31" rx="10" ry="21"><title>Dedo 4</title></ellipse>
            <ellipse class="zona-path" data-zona="dedo_3" cx="53" cy="26" rx="11" ry="23"><title>Dedo 3</title></ellipse>
            <ellipse class="zona-path" data-zona="dedo_2" cx="73" cy="29" rx="10" ry="22"><title>Dedo 2</title></ellipse>
            <ellipse class="zona-path" data-zona="dedo_1" cx="96" cy="38" rx="15" ry="27"><title>Dedo 1 (Hallux)</title></ellipse>

            {{-- ── UÑAS (encima de los dedos) ──────────────────── --}}
            <ellipse class="zona-path nail-zona" data-zona="uña_5" cx="14" cy="26" rx="6"   ry="7" ><title>Uña D5</title></ellipse>
            <ellipse class="zona-path nail-zona" data-zona="uña_4" cx="33" cy="14" rx="6.5" ry="7.5"><title>Uña D4</title></ellipse>
            <ellipse class="zona-path nail-zona" data-zona="uña_3" cx="53" cy="8"  rx="7.5" ry="8.5"><title>Uña D3</title></ellipse>
            <ellipse class="zona-path nail-zona" data-zona="uña_2" cx="73" cy="11" rx="7"   ry="8"  ><title>Uña D2</title></ellipse>
            <ellipse class="zona-path nail-zona" data-zona="uña_1" cx="96" cy="17" rx="10"  ry="10" ><title>Uña D1</title></ellipse>

            {{-- Etiquetas de orientación --}}
            <text class="foot-label" x="55" y="244" text-anchor="middle">Talón</text>
            <text class="foot-label" x="55" y="98"  text-anchor="middle">Antepié</text>
            <text class="foot-label" x="35" y="170" text-anchor="middle">Planta</text>
            <text class="foot-label foot-label--sm" x="98" y="165" text-anchor="middle">Arco</text>
        </svg>

        <div class="atc-zonas-chips" id="chips-izquierdo">
            <span class="atc-chips-empty">Sin zonas seleccionadas</span>
        </div>
    </div>

    {{-- PIE DERECHO (mirror del izquierdo usando transform SVG) --}}
    <div class="atc-pie-panel">
        <div class="atc-pie-titulo">
            Pie Derecho <i class="ti ti-arrow-right"></i>
        </div>

        <svg class="foot-svg" viewBox="0 0 112 270" xmlns="http://www.w3.org/2000/svg"
             data-pie="derecho" role="img" aria-label="Diagrama pie derecho">

            {{-- Todo el contenido está espejado horizontalmente --}}
            <g transform="translate(112,0) scale(-1,1)">
                <path class="foot-bg-outline"
                      d="M 15 66 Q 3 90 6 120 Q 1 155 5 195 Q 5 232 22 252 Q 55 270 90 252 Q 107 232 107 195 Q 112 155 106 120 Q 109 88 105 66 Q 88 54 56 52 Q 28 51 15 66 Z"/>

                <ellipse class="zona-path" data-zona="talon"  cx="55" cy="237" rx="39" ry="25"><title>Talón</title></ellipse>
                <path class="zona-path" data-zona="arco"      d="M 76 114 L 107 114 Q 112 155 107 200 L 80 218 Q 95 175 90 114 Z"><title>Arco plantar</title></path>
                <path class="zona-path" data-zona="planta"    d="M 6 118 Q 4 165 7 218 L 80 218 L 90 114 L 76 114 Q 55 107 32 110 Q 14 113 6 118 Z"><title>Planta</title></path>
                <path class="zona-path" data-zona="antepi"    d="M 6 68 Q 3 92 6 118 Q 18 110 38 108 Q 56 106 76 114 Q 94 108 107 118 Q 110 90 105 68 Q 88 56 56 54 Q 28 53 6 68 Z"><title>Antepié</title></path>

                <ellipse class="zona-path" data-zona="dedo_5" cx="14" cy="42" rx="9"  ry="20"><title>Dedo 5</title></ellipse>
                <ellipse class="zona-path" data-zona="dedo_4" cx="33" cy="31" rx="10" ry="21"><title>Dedo 4</title></ellipse>
                <ellipse class="zona-path" data-zona="dedo_3" cx="53" cy="26" rx="11" ry="23"><title>Dedo 3</title></ellipse>
                <ellipse class="zona-path" data-zona="dedo_2" cx="73" cy="29" rx="10" ry="22"><title>Dedo 2</title></ellipse>
                <ellipse class="zona-path" data-zona="dedo_1" cx="96" cy="38" rx="15" ry="27"><title>Dedo 1 (Hallux)</title></ellipse>

                <ellipse class="zona-path nail-zona" data-zona="uña_5" cx="14" cy="26" rx="6"   ry="7"  ><title>Uña D5</title></ellipse>
                <ellipse class="zona-path nail-zona" data-zona="uña_4" cx="33" cy="14" rx="6.5" ry="7.5"><title>Uña D4</title></ellipse>
                <ellipse class="zona-path nail-zona" data-zona="uña_3" cx="53" cy="8"  rx="7.5" ry="8.5"><title>Uña D3</title></ellipse>
                <ellipse class="zona-path nail-zona" data-zona="uña_2" cx="73" cy="11" rx="7"   ry="8"  ><title>Uña D2</title></ellipse>
                <ellipse class="zona-path nail-zona" data-zona="uña_1" cx="96" cy="17" rx="10"  ry="10" ><title>Uña D1</title></ellipse>
            </g>

            {{-- Etiquetas (no se espejean, posiciones calculadas manualmente) --}}
            <text class="foot-label" x="57" y="244" text-anchor="middle">Talón</text>
            <text class="foot-label" x="57" y="98"  text-anchor="middle">Antepié</text>
            <text class="foot-label" x="77" y="170" text-anchor="middle">Planta</text>
            <text class="foot-label foot-label--sm" x="16" y="165" text-anchor="middle">Arco</text>
        </svg>

        <div class="atc-zonas-chips" id="chips-derecho">
            <span class="atc-chips-empty">Sin zonas seleccionadas</span>
        </div>
    </div>

</div>

{{-- Leyenda --}}
<div class="atc-zonas-legend">
    <div class="atc-legend-item"><span class="atc-legend-dot" style="background:rgba(var(--primary),1)"></span> Zona afectada</div>
    <div class="atc-legend-item"><span class="atc-legend-dot atc-legend-dot--nail"></span> Uña (zona encima del dedo)</div>
    <div class="atc-legend-item"><i class="ti ti-info-circle" style="font-size:.85rem;color:#94a3b8"></i> Clic para seleccionar/deseleccionar</div>
</div>
