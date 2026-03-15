@props([
    'empresa' => null,
])

<form
    action="{{ route('settings.empresa.update') }}"
    method="POST"
    enctype="multipart/form-data"
    class="settings-form"
>
    @csrf
    @method('PUT')

    <!-- Campo oculto para tipo_identificacion -->
    <input type="hidden" name="tipo_identificacion" value="RUC">

    <h6 class="setting-section-title mb-3">Datos Principales</h6>

    <div class="row g-3">
        <x-ui.input
            name="nombre"
            label="Nombre de la Empresa"
            :value="old('nombre', $empresa?->nombre ?? '')"
            placeholder="Nombre comercial de la empresa"
            :required="true"
        />

        <div class="col-md-6">
            <x-ui.input
                name="identificacion"
                label="Identificación / RUC / NIT"
                :value="old('identificacion', $empresa?->identificacion ?? '')"
                placeholder="Número de identificación fiscal"
                :required="true"
            />
        </div>

        <div class="col-md-6">
            <x-ui.input
                name="telefono"
                label="Teléfono"
                :value="old('telefono', $empresa?->telefono ?? '')"
                placeholder="+506 0000 0000"
                icon="ti ti-phone"
            />
        </div>

        <div class="col-12">
            <x-ui.input
                name="email"
                label="Correo Electrónico"
                :value="old('email', $empresa?->email ?? '')"
                placeholder="contacto@empresa.com"
                type="email"
                :required="true"
                icon="ti ti-mail"
            />
        </div>

        <div class="col-12">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea
                name="direccion"
                id="direccion"
                class="form-control @error('direccion') is-invalid @enderror"
                rows="3"
                placeholder="Dirección completa de la empresa"
            >{{ old('direccion', $empresa?->direccion ?? '') }}</textarea>

            @error('direccion')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <h6 class="setting-section-title mb-3 mt-5">Logos</h6>
    <div class="row g-4">
        <!-- Logo Redondo (Login) -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="mb-3">Logo Redondo (Login)</h6>
                    <x-settings.tabs.empresa.logo-upload :empresa="$empresa" name="logo" />
                    <small class="text-muted d-block mt-2">Se muestra en el login</small>
                </div>
            </div>
        </div>

        <!-- Logo Rectangular (Menú) -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="mb-3">Logo Rectangular (Menú)</h6>
                    <x-settings.tabs.empresa.logo-upload :empresa="$empresa" name="logo_rectangular" />
                    <small class="text-muted d-block mt-2">Se muestra en el sidebar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end mt-4">
        <x-ui.button
            type="submit"
            variant="primary"
            icon="ti "
        >
            Guardar Cambios
        </x-ui.button>
    </div>
</form>
