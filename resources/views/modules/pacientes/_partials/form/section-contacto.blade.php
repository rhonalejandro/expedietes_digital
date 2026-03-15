@php
    $persona = isset($paciente) ? $paciente->persona : null;
@endphp

<div class="card border-0 pac-detail-card mb-4">
    <div class="card-body">
        <h6 class="pac-section-title mb-3">Información de Contacto</h6>
        <div class="row g-3">

            <div class="col-sm-6">
                <x-ui.input
                    name="email"
                    type="email"
                    label="Correo Electrónico"
                    :value="old('email', $persona->email ?? '')"
                    placeholder="correo@ejemplo.com"
                    icon="ti ti-mail"
                />
            </div>

            <div class="col-sm-6">
                <x-ui.input
                    name="contacto"
                    label="Teléfono"
                    :value="old('contacto', $persona->contacto ?? '')"
                    placeholder="Ej. +505 8888-8888"
                    icon="ti ti-phone"
                />
            </div>

            <div class="col-sm-12">
                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea
                        name="direccion"
                        class="form-control @error('direccion') is-invalid @enderror"
                        rows="2"
                        placeholder="Dirección completa del paciente"
                    >{{ old('direccion', $persona->direccion ?? '') }}</textarea>
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6">
                <x-ui.input
                    name="contacto_emergencia"
                    label="Contacto de Emergencia"
                    :value="old('contacto_emergencia', $persona->contacto_emergencia ?? '')"
                    placeholder="Nombre y teléfono"
                />
            </div>

        </div>
    </div>
</div>
