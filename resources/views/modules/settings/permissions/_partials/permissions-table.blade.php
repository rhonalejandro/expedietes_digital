{{-- 
Desarrollo - Permisos de Usuarios - UI - 2026-02-18
Partial: permissions-table.blade.php (< 100 líneas)
--}}

<div class="card settings-card">
    <div class="card-header bg-transparent border-0">
        <h5 class="mb-0">
            <i class="ti ti-shield me-2"></i>
            Permisos Registrados
        </h5>
    </div>
    <div class="card-body">
        @if(empty($permisos))
            @include('modules.settings.permissions._partials.empty-state')
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Módulo</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permisos as $modulo => $permisosModulo)
                            @foreach($permisosModulo as $permiso)
                                @include('modules.settings.permissions._components.permission-row', [
                                    'permiso' => (object) $permiso,
                                    'modulo' => $modulo
                                ])
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
