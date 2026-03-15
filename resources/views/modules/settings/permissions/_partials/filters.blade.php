{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Partial: filters.blade.php (< 50 líneas)
--}}

<div class="card settings-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('settings.permissions.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Filtrar por Módulo</label>
                <select name="modulo" class="form-select">
                    <option value="all">Todos los módulos</option>
                    @foreach($modulos as $mod)
                        <option value="{{ $mod }}" {{ $modulo == $mod ? 'selected' : '' }}>
                            {{ ucfirst($mod) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="all">Todos</option>
                    <option value="general" {{ $tipo == 'general' ? 'selected' : '' }}>General</option>
                    <option value="granular" {{ $tipo == 'granular' ? 'selected' : '' }}>Granular</option>
                </select>
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="ti ti-search me-1"></i> Filtrar
                </button>
                <a href="{{ route('settings.permissions.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-x"></i>
                </a>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('settings.permissions.create') }}" class="btn btn-success w-100">
                    <i class="ti ti-plus me-1"></i> Nuevo Permiso
                </a>
            </div>
        </form>
    </div>
</div>
