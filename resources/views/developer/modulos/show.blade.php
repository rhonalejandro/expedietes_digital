@extends('developer.layouts.master')

@section('title', $modulo->nombre . ' — Acciones')

@section('content')

    {{-- Header ----------------------------------------------------------------}}
    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="color: #8b949e; font-size: 13px;">
                <a href="{{ route('developer.modulos.index') }}" style="color: #8b949e;" class="text-decoration-none">Módulos</a>
                <span>/</span>
                <span style="color: #e6edf3;">{{ $modulo->nombre }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <i class="{{ $modulo->icono }}" style="font-size: 2rem; color: #667eea;"></i>
                <div>
                    <h4 class="mb-0 fw-bold" style="color: #e6edf3;">{{ $modulo->nombre }}</h4>
                    <code class="dev-code">{{ $modulo->slug }}</code>
                    @if ($modulo->url)
                        <span style="color: #8b949e; font-size: 12px; margin-left: 8px;">{{ $modulo->url }}</span>
                    @endif
                </div>
            </div>
            @if ($modulo->descripcion)
                <p class="mt-2 mb-0" style="color: #8b949e; font-size: 13px;">{{ $modulo->descripcion }}</p>
            @endif
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('developer.acciones.create', $modulo->id) }}" class="btn dev-btn-primary">
                <i class="ti ti-plus me-1"></i>Nueva Acción
            </a>
            <a href="{{ route('developer.modulos.edit', $modulo->id) }}" class="btn btn-outline-secondary"
               style="border-color: #30363d; color: #c9d1d9;">
                <i class="ti ti-pencil me-1"></i>Editar módulo
            </a>
        </div>
    </div>

    {{-- Helper reference -------------------------------------------------------}}
    <div class="dev-card p-3 mb-4" style="border-color: rgba(102,126,234,.3);">
        <p class="dev-label mb-2">Uso en controladores y vistas</p>
        <div class="d-flex flex-wrap gap-2">
            <code class="dev-code">hasPermission('{{ $modulo->slug }}')</code>
            <code class="dev-code">canView('{{ $modulo->slug }}')</code>
            <code class="dev-code">canCreate('{{ $modulo->slug }}')</code>
            <code class="dev-code">canEdit('{{ $modulo->slug }}')</code>
            <code class="dev-code">canDelete('{{ $modulo->slug }}')</code>
            <code class="dev-code">canDo('{{ $modulo->slug }}', 'accion_code')</code>
        </div>
    </div>

    {{-- Tabla de acciones -------------------------------------------------------}}
    <div class="dev-card">
        <div class="d-flex align-items-center justify-content-between px-3 pt-3 pb-2" style="border-bottom: 1px solid #30363d;">
            <h6 class="mb-0 fw-semibold" style="color: #e6edf3;">
                Acciones del Módulo
                <span class="dev-badge ms-2">{{ $permisos->count() }}</span>
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table dev-table mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Código (access_code)</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permisos as $permiso)
                        <tr>
                            <td>
                                <span style="color: #e6edf3;">{{ $permiso->nombre }}</span>
                                @if ($permiso->descripcion)
                                    <br><small style="color: #8b949e;">{{ $permiso->descripcion }}</small>
                                @endif
                            </td>
                            <td><code class="dev-code">{{ $permiso->codigo }}</code></td>
                            <td>
                                <span class="badge" style="background: {{ $permiso->tipo === 'general' ? 'rgba(102,126,234,.15)' : 'rgba(227,179,65,.15)' }}; color: {{ $permiso->tipo === 'general' ? '#667eea' : '#e3b341' }};">
                                    {{ $permiso->tipo }}
                                </span>
                            </td>
                            <td>
                                <span style="color: {{ $permiso->estado ? '#3fb950' : '#8b949e' }}; font-size: 12px;">
                                    <i class="ti ti-{{ $permiso->estado ? 'check' : 'x' }} me-1"></i>
                                    {{ $permiso->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('developer.acciones.edit', [$modulo->id, $permiso->id]) }}"
                                       class="btn btn-sm btn-outline-secondary" style="border-color: #30363d; color: #c9d1d9;" title="Editar">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('developer.acciones.destroy', [$modulo->id, $permiso->id]) }}"
                                          class="d-inline form-delete-accion">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                data-nombre="{{ $permiso->nombre }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5" style="color: #8b949e;">
                                <i class="ti ti-key-off d-block mb-2" style="font-size: 2rem;"></i>
                                Sin acciones registradas.
                                <a href="{{ route('developer.acciones.create', $modulo->id) }}" style="color: #667eea;">Agrega la primera.</a>
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
document.querySelectorAll('.form-delete-accion').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const nombre = this.querySelector('button').dataset.nombre;
        if (confirm(`¿Eliminar la acción "${nombre}"? Solo es posible si no está asignada a usuarios o plantillas.`)) {
            this.submit();
        }
    });
});
</script>
@endpush
