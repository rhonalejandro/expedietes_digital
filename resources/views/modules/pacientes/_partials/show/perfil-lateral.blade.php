{{--
    Columna izquierda: avatar, nombre, estado y todos los datos del paciente en lista vertical
--}}
<div class="card border-0 pac-detail-card">
    <div class="card-body p-0">

        {{-- Avatar + nombre + estado --}}
        <div class="pac-perfil-top p-4 text-center border-bottom">
            <div class="pac-avatar-lg mx-auto mb-3" aria-hidden="true">
                {{ strtoupper(substr($paciente->persona->nombre ?? '?', 0, 1)) }}{{ strtoupper(substr($paciente->persona->apellido ?? '', 0, 1)) }}
            </div>
            <h5 class="fw-semibold text-dark mb-1">{{ $paciente->nombre_completo }}</h5>
            <p class="text-muted small mb-3">{{ $paciente->persona->email ?? 'Sin correo' }}</p>
            <div class="d-flex justify-content-center gap-2 mb-3">
                <span class="badge {{ $paciente->estado ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                    {{ $paciente->estado ? 'Activo' : 'Inactivo' }}
                </span>
                <span class="badge bg-primary-subtle text-primary">Paciente</span>
            </div>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-pencil me-1"></i>Editar
                </a>
                <a href="{{ route('pacientes.index') }}" class="btn btn-sm btn-light">
                    <i class="ti ti-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="d-flex border-bottom text-center">
            <div class="flex-fill py-3 border-end">
                <h6 class="mb-0 fw-semibold">{{ $paciente->casos->count() }}</h6>
                <small class="text-muted">Casos</small>
            </div>
            <div class="flex-fill py-3 border-end">
                <h6 class="mb-0 fw-semibold">{{ $paciente->citas->count() }}</h6>
                <small class="text-muted">Citas</small>
            </div>
            <div class="flex-fill py-3">
                <h6 class="mb-0 fw-semibold">{{ $paciente->created_at->format('d/m/Y') }}</h6>
                <small class="text-muted">Registro</small>
            </div>
        </div>

        {{-- Lista de datos --}}
        <div class="p-4">

            <p class="pac-label fw-semibold text-uppercase mb-3" style="font-size:0.7rem; letter-spacing:.06em;">
                Datos Personales
            </p>

            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-id-badge',
                'label' => 'Nombre completo',
                'value' => $paciente->nombre_completo,
            ])
            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-credit-card',
                'label' => 'Identificación',
                'value' => ($paciente->persona->tipo_identificacion ?? '') . ' ' . ($paciente->persona->identificacion ?? '—'),
            ])
            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-cake',
                'label' => 'Fecha de nacimiento',
                'value' => $paciente->persona->fecha_nacimiento
                    ? \Carbon\Carbon::parse($paciente->persona->fecha_nacimiento)->format('d/m/Y') . ' (' . \Carbon\Carbon::parse($paciente->persona->fecha_nacimiento)->age . ' años)'
                    : '—',
            ])
            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-gender-bigender',
                'label' => 'Género',
                'value' => ucfirst($paciente->persona->genero ?? '—'),
            ])
            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-world',
                'label' => 'Nacionalidad',
                'value' => $paciente->persona->nacionalidad ?? '—',
            ])
            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-briefcase',
                'label' => 'Ocupación',
                'value' => $paciente->persona->ocupacion ?? '—',
            ])

            <p class="pac-label fw-semibold text-uppercase mt-4 mb-3" style="font-size:0.7rem; letter-spacing:.06em;">
                Contacto
            </p>

            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-mail',
                'label' => 'Correo',
                'value' => $paciente->persona->email ?? '—',
            ])
            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-phone',
                'label' => 'Teléfono',
                'value' => $paciente->persona->contacto ?? '—',
            ])
            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-map-pin',
                'label' => 'Dirección',
                'value' => $paciente->persona->direccion ?? '—',
            ])
            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-phone-call',
                'label' => 'Emergencia',
                'value' => $paciente->persona->contacto_emergencia ?? '—',
            ])

            <p class="pac-label fw-semibold text-uppercase mt-4 mb-3" style="font-size:0.7rem; letter-spacing:.06em;">
                Clínica
            </p>

            @include('modules.pacientes._partials.show.perfil-campo', [
                'icon'  => 'ti-heart-rate-monitor',
                'label' => 'Seguro médico',
                'value' => $paciente->persona->seguro_medico ?? '—',
            ])
        </div>

    </div>
</div>
