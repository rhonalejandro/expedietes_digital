<div class="citas-header">

    {{-- Navegación izquierda --}}
    <div class="citas-header-nav">
        <button class="btn-hoy" id="btn-hoy">Hoy</button>
        <button class="citas-nav-btn" id="btn-prev">
            <i class="ti ti-chevron-left"></i>
        </button>
        <button class="citas-nav-btn" id="btn-next">
            <i class="ti ti-chevron-right"></i>
        </button>
        <span id="citas-date-display" class="citas-header-date ms-1"></span>
    </div>

    {{-- Centro: filtro de sucursal --}}
    <div class="d-flex align-items-center gap-2">
        <i class="ti ti-building-store" style="font-size:1rem;color:#8a94a6;"></i>
        <select id="filtro-sucursal" class="form-select form-select-sm" style="min-width:160px;font-size:.8rem;">
            <option value="">Todas las sucursales</option>
            @foreach(\App\Models\Sucursal::where('estado', true)->get() as $suc)
                <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
            @endforeach
        </select>
    </div>

    {{-- Derecha: tabs de vista + ajustar + nueva cita --}}
    <div class="d-flex align-items-center gap-3">
        <div class="citas-view-tabs">
            <button class="citas-view-tab" data-view="dia">Día</button>
            <button class="citas-view-tab active" data-view="semana">Semana</button>
            <button class="citas-view-tab" data-view="recursos">Recursos</button>
        </div>

        {{-- Botón Ajustar: solo visible en vista Recursos --}}
        <button type="button" id="btn-ajustar" class="citas-ajustar-btn" style="display:none;" title="Ajustar todos los especialistas en pantalla">
            <i class="ti ti-layout-columns"></i>
            <span>Ajustar</span>
        </button>

        <button type="button" class="btn btn-sm btn-primary"
                data-bs-toggle="modal" data-bs-target="#modal-crear-cita">
            <i class="ti ti-plus me-1"></i>Nueva Cita
        </button>
    </div>

</div>
