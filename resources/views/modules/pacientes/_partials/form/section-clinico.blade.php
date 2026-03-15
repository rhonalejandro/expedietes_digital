@php
    $persona = isset($paciente) ? $paciente->persona : null;
    // Estado actual: en create siempre true; en edit, el valor del paciente
    $estadoActual = isset($paciente) ? $paciente->estado : true;
@endphp

<div class="card border-0 pac-detail-card mb-4">
    <div class="card-body">
        <h6 class="pac-section-title mb-3">Información Clínica</h6>
        <div class="row g-3">

            <div class="col-sm-6">
                <x-ui.input
                    name="ocupacion"
                    label="Ocupación"
                    :value="old('ocupacion', $persona->ocupacion ?? '')"
                    placeholder="Ej. Docente, Contador..."
                />
            </div>

            <div class="col-sm-6">
                <x-ui.input
                    name="nacionalidad"
                    label="Nacionalidad"
                    :value="old('nacionalidad', $persona->nacionalidad ?? '')"
                    placeholder="Ej. Nicaragüense"
                />
            </div>

            <div class="col-sm-6">
                <x-ui.input
                    name="seguro_medico"
                    label="Seguro Médico"
                    :value="old('seguro_medico', $persona->seguro_medico ?? '')"
                    placeholder="Nombre de la aseguradora"
                />
            </div>

            {{-- Toggle estado (solo visible en edición) --}}
            @if (isset($paciente))
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label">Estado del Paciente</label>
                        <div class="mt-1">
                            <x-ui.toggle
                                name="estado"
                                label="Paciente activo"
                                :checked="old('estado', $estadoActual)"
                                value="1"
                            />
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
