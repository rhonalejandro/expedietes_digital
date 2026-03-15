<div class="card border-0 pac-table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table pac-table mb-0">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Identificación</th>
                        <th>Teléfono</th>
                        <th>Seguro Médico</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pacientes as $paciente)
                        <tr>
                            {{-- Columna paciente: avatar + nombre + email --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pac-avatar-sm" aria-hidden="true">
                                        {{ strtoupper(substr($paciente->persona->nombre ?? '?', 0, 1)) }}{{ strtoupper(substr($paciente->persona->apellido ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-medium text-dark">
                                            {{ $paciente->persona->nombre ?? '—' }}
                                            {{ $paciente->persona->apellido ?? '' }}
                                        </p>
                                        <small class="text-muted">{{ $paciente->persona->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Identificación --}}
                            <td>
                                <small class="pac-label d-block">
                                    {{ $paciente->persona->tipo_identificacion ?? '—' }}
                                </small>
                                {{ $paciente->persona->identificacion ?? '—' }}
                            </td>

                            {{-- Teléfono --}}
                            <td>{{ $paciente->persona->contacto ?? '—' }}</td>

                            {{-- Seguro médico --}}
                            <td>{{ $paciente->persona->seguro_medico ?? '—' }}</td>

                            {{-- Estado badge --}}
                            <td class="text-center">
                                <span class="badge {{ $paciente->estado
                                    ? 'bg-success-subtle text-success'
                                    : 'bg-secondary-subtle text-secondary' }}">
                                    {{ $paciente->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            {{-- Acciones: Ver / Editar / Toggle / Eliminar --}}
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('pacientes.show', $paciente->id) }}"
                                       class="pac-action-btn"
                                       title="Ver perfil">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    <a href="{{ route('pacientes.edit', $paciente->id) }}"
                                       class="pac-action-btn"
                                       title="Editar">
                                        <i class="ti ti-pencil"></i>
                                    </a>

                                    <button type="button"
                                            class="pac-action-btn btn-toggle-estado"
                                            data-id="{{ $paciente->id }}"
                                            data-estado="{{ $paciente->estado ? '1' : '0' }}"
                                            title="{{ $paciente->estado ? 'Desactivar' : 'Activar' }}">
                                        <i class="ti ti-{{ $paciente->estado ? 'toggle-right text-success' : 'toggle-left text-secondary' }}"></i>
                                    </button>

                                    <button type="button"
                                            class="pac-action-btn pac-action-btn--delete btn-delete-paciente"
                                            data-id="{{ $paciente->id }}"
                                            data-nombre="{{ $paciente->nombre_completo }}"
                                            title="Eliminar">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="pac-empty-state">
                                    <i class="ti ti-users-off d-block mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-1">No se encontraron pacientes</p>
                                    @if (request()->hasAny(['q', 'estado']))
                                        <a href="{{ route('pacientes.index') }}" class="btn btn-sm btn-light">
                                            <i class="ti ti-x me-1"></i>Limpiar filtros
                                        </a>
                                    @else
                                        <a href="{{ route('pacientes.create') }}" class="btn btn-sm btn-primary mt-1">
                                            <i class="ti ti-user-plus me-1"></i>Registrar primer paciente
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación Laravel --}}
        @if ($pacientes->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $pacientes->links() }}
            </div>
        @endif
    </div>
</div>
