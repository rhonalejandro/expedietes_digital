{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Vista: permissions.blade.php (Tab en settings index)
--}}

<div class="tab-pane fade" id="permisos" role="tabpanel">
    <div class="card settings-card">
        <div class="card-header bg-transparent border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-shield-lock me-2"></i>
                    Gestión de Permisos
                </h5>
                <a href="{{ route('settings.permissions.index') }}" class="btn btn-primary">
                    <i class="ti ti-external-link me-1"></i> Abrir Módulo
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="text-center py-5">
                <div class="bg-primary bg-opacity-10 h-100 w-100 d-flex-center b-r-50 m-auto mb-3">
                    <i class="ti ti-shield-lock f-s-40 text-primary"></i>
                </div>
                <h5 class="text-muted mb-2">Módulo de Permisos</h5>
                <p class="text-muted mb-4">
                    Gestiona permisos generales y granulares del sistema
                </p>
                <div class="row g-3 justify-content-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="h3 mb-0 text-primary">{{ $stats['total_permisos'] ?? 0 }}</div>
                            <div class="text-muted small">Total Permisos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="h3 mb-0 text-success">{{ $stats['permisos_activos'] ?? 0 }}</div>
                            <div class="text-muted small">Activos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="h3 mb-0 text-info">{{ $stats['total_modulos'] ?? 0 }}</div>
                            <div class="text-muted small">Módulos</div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('settings.permissions.index') }}" class="btn btn-primary me-2">
                        <i class="ti ti-shield me-1"></i> Ver Permisos
                    </a>
                    <a href="{{ route('settings.permissions.create') }}" class="btn btn-outline-primary">
                        <i class="ti ti-plus me-1"></i> Crear Permiso
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
