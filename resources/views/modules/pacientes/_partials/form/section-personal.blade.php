@php
    // Soporte para create (sin $paciente) y edit (con $paciente)
    $persona = isset($paciente) ? $paciente->persona : null;
@endphp

<div class="card border-0 pac-detail-card mb-4">
    <div class="card-body">
        <h6 class="pac-section-title mb-3">Datos Personales</h6>
        <div class="row g-3">

            <div class="col-sm-6">
                <x-ui.input
                    name="nombre"
                    label="Nombre"
                    :value="old('nombre', $persona->nombre ?? '')"
                    placeholder="Nombre del paciente"
                    :required="true"
                />
            </div>

            <div class="col-sm-6">
                <x-ui.input
                    name="apellido"
                    label="Apellido"
                    :value="old('apellido', $persona->apellido ?? '')"
                    placeholder="Apellido del paciente"
                    :required="true"
                />
            </div>

            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">
                        Tipo de Identificación <span class="text-danger">*</span>
                    </label>
                    <select name="tipo_identificacion"
                            class="form-select @error('tipo_identificacion') is-invalid @enderror"
                            required>
                        <option value="">Seleccionar...</option>
                        @foreach (['Cédula', 'Pasaporte', 'Carnet de extranjería', 'Otro'] as $tipo)
                            <option value="{{ $tipo }}"
                                {{ old('tipo_identificacion', $persona->tipo_identificacion ?? '') === $tipo ? 'selected' : '' }}>
                                {{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_identificacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6">
                <x-ui.input
                    name="identificacion"
                    label="Número de Identificación"
                    :value="old('identificacion', $persona->identificacion ?? '')"
                    placeholder="Ej. 001-000000-0000X"
                    :required="true"
                />
            </div>

            <div class="col-sm-6">
                <x-ui.input
                    name="fecha_nacimiento"
                    type="date"
                    label="Fecha de Nacimiento"
                    :value="old('fecha_nacimiento', $persona && $persona->fecha_nacimiento
                        ? \Carbon\Carbon::parse($persona->fecha_nacimiento)->format('Y-m-d')
                        : '')"
                />
            </div>

            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">Género</label>
                    <select name="genero"
                            class="form-select @error('genero') is-invalid @enderror">
                        <option value="">Sin especificar</option>
                        <option value="masculino"
                            {{ old('genero', $persona->genero ?? '') === 'masculino' ? 'selected' : '' }}>
                            Masculino
                        </option>
                        <option value="femenino"
                            {{ old('genero', $persona->genero ?? '') === 'femenino' ? 'selected' : '' }}>
                            Femenino
                        </option>
                        <option value="otro"
                            {{ old('genero', $persona->genero ?? '') === 'otro' ? 'selected' : '' }}>
                            Otro
                        </option>
                    </select>
                    @error('genero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>
    </div>
</div>
