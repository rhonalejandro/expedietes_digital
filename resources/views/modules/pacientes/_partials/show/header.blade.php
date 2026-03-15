<div class="card border-0 pac-show-header mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-start gap-4 flex-wrap">

            {{-- Avatar con iniciales --}}
            <div class="pac-avatar-lg flex-shrink-0" aria-hidden="true">
                {{ strtoupper(substr($paciente->persona->nombre ?? '?', 0, 1)) }}{{ strtoupper(substr($paciente->persona->apellido ?? '', 0, 1)) }}
            </div>

            {{-- Info principal --}}
            <div class="flex-grow-1">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                    <div>
                        <h4 class="mb-1 fw-semibold text-dark">{{ $paciente->nombre_completo }}</h4>
                        <p class="text-muted mb-2">{{ $paciente->persona->email ?? 'Sin correo registrado' }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge {{ $paciente->estado
                                ? 'bg-success-subtle text-success'
                                : 'bg-secondary-subtle text-secondary' }}">
                                {{ $paciente->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                            <span class="badge bg-primary-subtle text-primary">Paciente</span>
                        </div>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="d-flex gap-2">
                        <a href="{{ route('pacientes.edit', $paciente->id) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-pencil me-1"></i>Editar
                        </a>
                        <a href="{{ route('pacientes.index') }}" class="btn btn-sm btn-light">
                            <i class="ti ti-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                {{-- Stats rápidas --}}
                <div class="row g-3 mt-2 pt-3 border-top">
                    <div class="col-auto">
                        <p class="pac-stat-label mb-0">Casos clínicos</p>
                        <h6 class="mb-0 fw-semibold">{{ $paciente->casos->count() }}</h6>
                    </div>
                    <div class="col-auto">
                        <p class="pac-stat-label mb-0">Citas</p>
                        <h6 class="mb-0 fw-semibold">{{ $paciente->citas->count() }}</h6>
                    </div>
                    <div class="col-auto">
                        <p class="pac-stat-label mb-0">Registrado</p>
                        <h6 class="mb-0 fw-semibold">{{ $paciente->created_at->format('d/m/Y') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
