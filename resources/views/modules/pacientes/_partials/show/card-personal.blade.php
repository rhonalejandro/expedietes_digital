<div class="card border-0 pac-detail-card mb-4">
    <div class="card-body">
        <h6 class="pac-section-title mb-3">Datos Personales</h6>
        <div class="row g-3">

            <div class="col-sm-6">
                <p class="pac-label mb-1">Nombre</p>
                <p class="pac-value mb-0">{{ $paciente->persona->nombre ?? '—' }}</p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Apellido</p>
                <p class="pac-value mb-0">{{ $paciente->persona->apellido ?? '—' }}</p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Tipo de Identificación</p>
                <p class="pac-value mb-0">{{ $paciente->persona->tipo_identificacion ?? '—' }}</p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Número de Identificación</p>
                <p class="pac-value mb-0">{{ $paciente->persona->identificacion ?? '—' }}</p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Fecha de Nacimiento</p>
                <p class="pac-value mb-0">
                    @if ($paciente->persona->fecha_nacimiento)
                        {{ \Carbon\Carbon::parse($paciente->persona->fecha_nacimiento)->format('d/m/Y') }}
                        <small class="text-muted ms-1">
                            ({{ \Carbon\Carbon::parse($paciente->persona->fecha_nacimiento)->age }} años)
                        </small>
                    @else
                        —
                    @endif
                </p>
            </div>

            <div class="col-sm-6">
                <p class="pac-label mb-1">Género</p>
                <p class="pac-value mb-0">{{ ucfirst($paciente->persona->genero ?? '—') }}</p>
            </div>

        </div>
    </div>
</div>
