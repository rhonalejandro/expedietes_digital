{{-- Panel derecho con 3 tabs: Información, Expedientes, Actividades --}}
<div class="card border-0 pac-detail-card">

    <div class="pac-tabs-nav">
        <ul class="nav nav-tabs pac-nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-info-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-info"
                    type="button" role="tab">
                    <i class="ti ti-user me-1"></i>Información
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-expedientes-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-expedientes"
                    type="button" role="tab">
                    <i class="ti ti-folder-open me-1"></i>Expedientes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-actividades-btn"
                    data-bs-toggle="tab" data-bs-target="#tab-actividades"
                    type="button" role="tab">
                    <i class="ti ti-history me-1"></i>Actividades
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content pac-tab-content">

        {{-- Tab 1: Información --}}
        <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
            @include('modules.pacientes._partials.show.card-personal')
            @include('modules.pacientes._partials.show.card-contacto')
            @include('modules.pacientes._partials.show.card-clinico')
        </div>

        {{-- Tab 2: Expedientes --}}
        <div class="tab-pane fade" id="tab-expedientes" role="tabpanel">
            @include('modules.pacientes._partials.show.card-casos')
        </div>

        {{-- Tab 3: Actividades Recientes --}}
        <div class="tab-pane fade" id="tab-actividades" role="tabpanel">
            <x-pacientes.actividades-recientes :clienteId="$paciente->id" />
        </div>

    </div>
</div>
