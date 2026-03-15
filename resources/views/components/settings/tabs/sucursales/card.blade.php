@props([
    'sucursal',
])

<div class="sucursal-card {{ $sucursal->estado ? 'active' : 'inactive' }}">
    <!-- Imagen de la sucursal -->
    <div class="sucursal-imagen-container mb-3">
        @if($sucursal->imagen)
            <img src="{{ asset('storage/' . $sucursal->imagen) }}" alt="{{ $sucursal->nombre }}" class="sucursal-imagen">
        @else
            <div class="sucursal-imagen d-flex-center bg-light">
                <i class="ti ti-building f-s-32 text-muted"></i>
            </div>
        @endif
    </div>

    <div class="d-flex justify-content-between align-items-start mb-2">
        <h6 class="mb-0 text-truncate flex-grow-1" title="{{ $sucursal->nombre }}">
            {{ $sucursal->nombre }}
        </h6>
        <!-- Switch para activar/desactivar -->
        <label class="toggle-switch ms-2" title="{{ $sucursal->estado ? 'Desactivar' : 'Activar' }}">
            <input type="checkbox" {{ $sucursal->estado ? 'checked' : '' }} onchange="toggleSucursal({{ $sucursal->id }})">
            <span class="toggle-slider"></span>
        </label>
    </div>

    <div class="mb-2">
        <small class="text-muted">
            <i class="ti ti-map-pin me-1"></i>
            {{ \Illuminate\Support\Str::limit($sucursal->direccion, 50) }}
        </small>
    </div>

    @if($sucursal->telefono ?? $sucursal->contacto)
    <div class="mb-2">
        <small class="text-muted">
            <i class="ti ti-phone me-1"></i>
            {{ $sucursal->telefono ?? $sucursal->contacto }}
        </small>
    </div>
    @endif

    @if($sucursal->email)
    <div class="mb-2">
        <small class="text-muted">
            <i class="ti ti-mail me-1"></i>
            {{ \Illuminate\Support\Str::limit($sucursal->email, 30) }}
        </small>
    </div>
    @endif

    @if($sucursal->encargado)
    <div class="mb-3">
        <small class="text-muted">
            <i class="ti ti-user me-1"></i>
            Encargado: {{ $sucursal->encargado }}
        </small>
    </div>
    @endif

    <div class="d-flex gap-2 mt-3">
        <button
            class="btn btn-sm btn-outline-primary flex-grow-1"
            onclick="editSucursal({{ json_encode($sucursal) }})"
            title="Editar"
        >
            <i class="ti ti-edit"></i>
        </button>

        <button
            class="btn btn-sm btn-outline-danger"
            onclick="deleteSucursal({{ $sucursal->id }})"
            title="Eliminar"
        >
            <i class="ti ti-trash"></i>
        </button>
    </div>
</div>
