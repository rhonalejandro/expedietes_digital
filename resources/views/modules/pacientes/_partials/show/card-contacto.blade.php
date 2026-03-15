<div class="card border-0 pac-detail-card mb-4">
    <div class="card-body">
        <h6 class="pac-section-title mb-3">Información de Contacto</h6>
        <div class="row g-3">

            <div class="col-sm-6">
                <p class="pac-label mb-1">Correo Electrónico</p>
                <p class="pac-value mb-0">{{ $paciente->persona->email ?? '—' }}</p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Teléfono</p>
                <p class="pac-value mb-0">{{ $paciente->persona->contacto ?? '—' }}</p>
            </div>

            <div class="col-sm-12">
                <p class="pac-label mb-1">Dirección</p>
                <p class="pac-value mb-0">{{ $paciente->persona->direccion ?? '—' }}</p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Contacto de Emergencia</p>
                <p class="pac-value mb-0">{{ $paciente->persona->contacto_emergencia ?? '—' }}</p>
            </div>

        </div>
    </div>
</div>
