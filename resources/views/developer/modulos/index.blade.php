@extends('developer.layouts.master')

@section('title', 'Módulos del Sistema')

@section('content')

    {{-- Header ----------------------------------------------------------------}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold" style="color: #e6edf3;">
                <i class="ti ti-layout-grid me-2" style="color: #667eea;"></i>Módulos del Sistema
            </h4>
            <p class="mb-0 mt-1" style="color: #8b949e; font-size: 13px;">
                Gestiona los módulos y sus acciones (permisos granulares)
            </p>
        </div>
        <a href="{{ route('developer.modulos.create') }}" class="btn dev-btn-primary">
            <i class="ti ti-plus me-1"></i>Nuevo Módulo
        </a>
    </div>

    {{-- Stats -----------------------------------------------------------------}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="dev-card p-3 d-flex align-items-center gap-3">
                <i class="ti ti-layout-grid" style="font-size: 1.5rem; color: #667eea;"></i>
                <div>
                    <p class="dev-label mb-0">Total Módulos</p>
                    <h5 class="mb-0 fw-bold" style="color: #e6edf3;">{{ $stats['total_modulos'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="dev-card p-3 d-flex align-items-center gap-3">
                <i class="ti ti-check" style="font-size: 1.5rem; color: #3fb950;"></i>
                <div>
                    <p class="dev-label mb-0">Activos</p>
                    <h5 class="mb-0 fw-bold" style="color: #e6edf3;">{{ $stats['total_activos'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="dev-card p-3 d-flex align-items-center gap-3">
                <i class="ti ti-key" style="font-size: 1.5rem; color: #e3b341;"></i>
                <div>
                    <p class="dev-label mb-0">Permisos registrados</p>
                    <h5 class="mb-0 fw-bold" style="color: #e6edf3;">{{ $stats['total_permisos'] }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de módulos -------------------------------------------------------}}
    <div class="dev-card">
        <div class="table-responsive">
            <table class="table dev-table mb-0">
                <thead>
                    <tr>
                        <th>Módulo</th>
                        <th>Slug</th>
                        <th>URL base</th>
                        <th class="text-center">Acciones</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Gestionar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modulos as $modulo)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $modulo->icono }}" style="color: #667eea; font-size: 1.1rem;"></i>
                                    <span class="fw-medium" style="color: #e6edf3;">{{ $modulo->nombre }}</span>
                                </div>
                                @if ($modulo->descripcion)
                                    <small style="color: #8b949e;">{{ Str::limit($modulo->descripcion, 60) }}</small>
                                @endif
                            </td>
                            <td><code class="dev-code">{{ $modulo->slug }}</code></td>
                            <td style="color: #8b949e;">{{ $modulo->url ?? '—' }}</td>
                            <td class="text-center">
                                <span class="dev-badge">{{ $modulo->total_acciones }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $modulo->activo ? 'bg-success' : 'bg-secondary' }} bg-opacity-25"
                                      style="color: {{ $modulo->activo ? '#3fb950' : '#8b949e' }};">
                                    {{ $modulo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('developer.modulos.show', $modulo->id) }}"
                                       class="btn btn-sm btn-outline-secondary" style="border-color: #30363d; color: #c9d1d9;" title="Ver acciones">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('developer.modulos.edit', $modulo->id) }}"
                                       class="btn btn-sm btn-outline-secondary" style="border-color: #30363d; color: #c9d1d9;" title="Editar">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('developer.modulos.destroy', $modulo->id) }}" class="d-inline form-delete">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                data-nombre="{{ $modulo->nombre }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color: #8b949e;">
                                <i class="ti ti-layout-grid-remove d-block mb-2" style="font-size: 2rem;"></i>
                                No hay módulos registrados.
                                <a href="{{ route('developer.modulos.create') }}" style="color: #667eea;">Crea el primero.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const nombre = this.querySelector('button').dataset.nombre;
        if (confirm(`¿Eliminar el módulo "${nombre}"? Esta acción no se puede deshacer.`)) {
            this.submit();
        }
    });
});
</script>
@endpush
