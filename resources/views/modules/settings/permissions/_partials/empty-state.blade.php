{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Partial: empty-state.blade.php (< 30 líneas)
--}}

<div class="text-center py-5">
    <div class="bg-light-primary h-100 w-100 d-flex-center b-r-50 m-auto mb-3">
        <i class="ti ti-shield-off f-s-40 text-primary"></i>
    </div>
    <h5 class="text-muted">No hay permisos registrados</h5>
    <p class="text-muted mb-3">Comienza creando el primer permiso del sistema</p>
    <a href="{{ route('settings.permissions.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Crear Permiso
    </a>
</div>
