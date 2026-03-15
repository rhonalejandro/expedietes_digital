<div class="card border-0 pac-detail-card mb-4">
    <div class="card-body">
        <h6 class="pac-section-title mb-3">Información Clínica</h6>
        <div class="row g-3">

            <div class="col-sm-6">
                <p class="pac-label mb-1">Seguro Médico</p>
                <p class="pac-value mb-0">{{ $paciente->persona->seguro_medico ?? '—' }}</p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Nacionalidad</p>
                <p class="pac-value mb-0">{{ $paciente->persona->nacionalidad ?? '—' }}</p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Ocupación</p>
                <p class="pac-value mb-0">{{ $paciente->persona->ocupacion ?? '—' }}</p>
            </div>

        </div>
    </div>
</div>
