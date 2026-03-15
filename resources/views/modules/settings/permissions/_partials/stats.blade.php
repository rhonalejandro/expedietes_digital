{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Partial: stats.blade.php (< 40 líneas)
--}}

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="p-3 bg-light rounded">
            <div class="text-muted small">Total Permisos</div>
            <div class="h3 mb-0 text-primary">{{ $stats['total_permisos'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 bg-light rounded">
            <div class="text-muted small">Permisos Activos</div>
            <div class="h3 mb-0 text-success">{{ $stats['permisos_activos'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 bg-light rounded">
            <div class="text-muted small">Permisos Inactivos</div>
            <div class="h3 mb-0 text-danger">{{ $stats['permisos_inactivos'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 bg-light rounded">
            <div class="text-muted small">Módulos</div>
            <div class="h3 mb-0 text-info">{{ $stats['total_modulos'] }}</div>
        </div>
    </div>
</div>
