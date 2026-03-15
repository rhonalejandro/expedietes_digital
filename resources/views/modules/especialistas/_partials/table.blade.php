<div class="card border-0 esp-table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table esp-table mb-0">
                <thead>
                    <tr>
                        <th>Especialista</th>
                        <th>Profesión / Especialidad</th>
                        <th>N.º Colegiado</th>
                        <th>Contacto</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($especialistas as $esp)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="esp-avatar-sm">
                                    {{ strtoupper(substr($esp->persona->nombre ?? '?', 0, 1)) }}{{ strtoupper(substr($esp->persona->apellido ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium text-dark">
                                        @if($esp->tratamiento && $esp->tratamiento !== 'N/A')
                                            <span class="esp-trato">{{ $esp->tratamiento }}</span>
                                        @endif
                                        {{ $esp->persona->nombre ?? '—' }} {{ $esp->persona->apellido ?? '' }}
                                    </p>
                                    <small class="text-muted">{{ $esp->email ?? $esp->persona->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="mb-0">{{ $esp->profesion ?? '—' }}</p>
                            @if($esp->especialidad)
                                <small class="text-muted">{{ $esp->especialidad }}</small>
                            @endif
                        </td>
                        <td>{{ $esp->num_colegiado ?? '—' }}</td>
                        <td>{{ $esp->telefono ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $esp->estado ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $esp->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('especialistas.show', $esp->id) }}"
                                   class="esp-action-btn" title="Ver perfil">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('especialistas.edit', $esp->id) }}"
                                   class="esp-action-btn" title="Editar">
                                    <i class="ti ti-pencil"></i>
                                </a>
                                <button type="button"
                                    class="esp-action-btn btn-toggle-esp"
                                    data-id="{{ $esp->id }}"
                                    data-estado="{{ $esp->estado ? '1' : '0' }}"
                                    title="{{ $esp->estado ? 'Desactivar' : 'Activar' }}">
                                    <i class="ti ti-{{ $esp->estado ? 'toggle-right text-success' : 'toggle-left text-secondary' }}"></i>
                                </button>
                                <button type="button"
                                    class="esp-action-btn esp-action-btn--delete btn-delete-esp"
                                    data-id="{{ $esp->id }}"
                                    data-nombre="{{ $esp->nombre_completo }}"
                                    title="Eliminar">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="esp-empty-state">
                                <i class="ti ti-users-off d-block mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-1">No se encontraron especialistas</p>
                                @if(request()->hasAny(['q', 'estado']))
                                    <a href="{{ route('especialistas.index') }}" class="btn btn-sm btn-light">
                                        <i class="ti ti-x me-1"></i>Limpiar filtros
                                    </a>
                                @else
                                    <a href="{{ route('especialistas.create') }}" class="btn btn-sm btn-primary mt-1">
                                        <i class="ti ti-user-plus me-1"></i>Registrar primer especialista
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($especialistas->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $especialistas->links() }}
            </div>
        @endif
    </div>
</div>
