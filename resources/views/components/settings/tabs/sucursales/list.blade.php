@props([
    'sucursales',
    'total',
    'activas',
])

<div class="sucursales-list">
    @if($sucursales->count() > 0)
        <div class="row g-4">
            @foreach($sucursales as $sucursal)
                <div class="col-md-6 col-xl-4">
                    <x-settings.tabs.sucursales.card :sucursal="$sucursal" />
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state text-center py-5">
            <div class="empty-state-icon">
                <i class="ti ti-building f-s-40 text-primary"></i>
            </div>
            <h5 class="text-muted mt-3">No hay sucursales registradas</h5>
            <p class="text-muted mb-3">Comienza agregando tu primera sucursal</p>
            <x-ui.button 
                variant="primary" 
                icon="ti ti-plus"
                data-bs-toggle="modal"
                data-bs-target="#addSucursalModal"
            >
                Agregar Sucursal
            </x-ui.button>
        </div>
    @endif
</div>
