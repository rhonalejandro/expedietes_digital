<aside class="citas-sidebar">

    <div class="citas-sidebar-inner">

        {{-- Buscador --}}
        <div class="sidebar-search-wrap">
            <i class="ti ti-search sidebar-search-icon"></i>
            <input type="text" id="sidebar-search" class="sidebar-search"
                   placeholder="Filtrar por paciente o servicio...">
            <button type="button" id="sidebar-search-clear" class="sidebar-search-clear" style="display:none" title="Limpiar">
                <i class="ti ti-x" style="left: 0px !important;"></i>
            </button>
        </div>

        {{-- Vista rápida --}}
        <div class="sidebar-section">
            <div class="sidebar-section-title">Vista rápida</div>
            <button class="sidebar-preset" data-preset="hoy">
                <i class="ti ti-calendar me-2"></i>Hoy
            </button>
            <button class="sidebar-preset" data-preset="manana">
                <i class="ti ti-calendar-plus me-2"></i>Mañana
            </button>
            <button class="sidebar-preset active" data-preset="semana">
                <i class="ti ti-calendar-week me-2"></i>Esta semana
            </button>
            <button class="sidebar-preset" data-preset="recursos">
                <i class="ti ti-layout-columns me-2"></i>Ver por especialista
            </button>
        </div>

        {{-- Especialistas --}}
        <div class="sidebar-section">
            <div class="sidebar-esp-header">
                <span class="sidebar-section-title mb-0">Mostrar sólo</span>
                <button class="sidebar-todos-btn" id="btn-sidebar-todos">Todos</button>
            </div>

            <div id="esp-filter-list">
                @foreach($especialistas as $esp)
                @php
                    $nombre   = $esp->nombre_completo;
                    $iniciales = collect(explode(' ', $nombre))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                @endphp
                <div class="esp-sidebar-item" data-id="{{ $esp->id }}">
                    <div class="esp-sidebar-avatar-sm">{{ $iniciales }}</div>
                    <span class="esp-sidebar-name" title="{{ $nombre }}">{{ $nombre }}</span>
                    <span class="esp-sidebar-count" data-id="{{ $esp->id }}">0</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Leyenda estatus — colores desde configuración de empresa --}}
    @php
        $coloresEmpresa = \App\Models\Empresa::first()?->colores_estatus ?? \App\Models\Empresa::COLORES_DEFAULT;
        $estadosLeyenda = [
            'pendiente'   => 'En espera de confirmación',
            'confirmada'  => 'Confirmada',
            'en_consulta' => 'En Consulta',
            'atendida'    => 'Atendida',
            'cancelada'   => 'Cancelada',
            'no_asistio'  => 'No asistió',
        ];
    @endphp
    <div class="citas-leyenda">
        <div class="citas-leyenda-title">Estado de citas</div>
        @foreach($estadosLeyenda as $key => $label)
        <div class="leyenda-item">
            <span class="leyenda-dot" style="background:{{ $coloresEmpresa[$key] ?? '#64748b' }};"></span>
            {{ $label }}
        </div>
        @endforeach
    </div>

</aside>
