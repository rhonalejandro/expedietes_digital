<div class="card border-0 pac-toolbar mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('pacientes.index') }}"
              class="d-flex gap-2 align-items-center flex-wrap">

            {{-- Búsqueda --}}
            <div class="flex-grow-1" style="min-width: 200px; max-width: 320px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text border-end-0 bg-white">
                        <i class="ti ti-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        name="q"
                        class="form-control border-start-0 ps-0"
                        placeholder="Nombre, apellido o identificación..."
                        value="{{ request('q') }}"
                    >
                </div>
            </div>

            {{-- Filtro estado --}}
            <select name="estado" class="form-select form-select-sm" style="width: auto; min-width: 150px;">
                <option value="">Todos los estados</option>
                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
            </select>

            {{-- Botón filtrar --}}
            <button type="submit" class="btn btn-sm btn-light border">
                <i class="ti ti-filter me-1"></i>Filtrar
            </button>

            {{-- Limpiar filtros --}}
            @if (request()->hasAny(['q', 'estado']))
                <a href="{{ route('pacientes.index') }}" class="btn btn-sm btn-link text-muted p-0">
                    <i class="ti ti-x me-1"></i>Limpiar
                </a>
            @endif

            {{-- Nuevo paciente --}}
            <div class="ms-auto">
                <a href="{{ route('pacientes.create') }}" class="btn btn-sm btn-primary">
                    <i class="ti ti-user-plus me-1"></i>Nuevo Paciente
                </a>
            </div>

        </form>
    </div>
</div>
