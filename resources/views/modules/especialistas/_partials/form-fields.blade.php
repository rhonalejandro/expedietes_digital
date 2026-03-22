{{--
    Partial compartido por create y edit.
    Variables requeridas: $tratamientos, $especialista (null en create)
--}}
@php $esp = $especialista ?? null; @endphp

{{-- Sección: Identificación profesional --}}
<div class="esp-card mb-4">
    <div class="card-body p-4">
        <h6 class="fw-semibold text-dark mb-4 pb-2 border-bottom">
            <i class="ti ti-id-badge me-2 text-primary"></i>Identificación Profesional
        </h6>
        <div class="row g-3">

            <div class="col-md-3">
                <label class="form-label esp-label">Tratamiento <span class="text-danger">*</span></label>
                <select name="tratamiento" class="form-select form-select-sm @error('tratamiento') is-invalid @enderror" required>
                    <option value="">Seleccionar...</option>
                    @foreach($tratamientos as $t)
                        <option value="{{ $t }}" @selected(old('tratamiento', $esp?->tratamiento) === $t)>{{ $t }}</option>
                    @endforeach
                </select>
                @error('tratamiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label esp-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                    value="{{ old('nombre', $esp?->persona?->nombre) }}" required>
                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-5">
                <label class="form-label esp-label">Apellido <span class="text-danger">*</span></label>
                <input type="text" name="apellido" class="form-control form-control-sm @error('apellido') is-invalid @enderror"
                    value="{{ old('apellido', $esp?->persona?->apellido) }}" required>
                @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-5">
                <label class="form-label esp-label">Profesión <span class="text-danger">*</span></label>
                <input type="text" name="profesion" class="form-control form-control-sm @error('profesion') is-invalid @enderror"
                    placeholder="Ej: Podólogo, Fisioterapeuta..."
                    value="{{ old('profesion', $esp?->profesion) }}" required>
                @error('profesion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label esp-label">Especialidad</label>
                <input type="text" name="especialidad" class="form-control form-control-sm @error('especialidad') is-invalid @enderror"
                    placeholder="Ej: Podología deportiva..."
                    value="{{ old('especialidad', $esp?->especialidad) }}">
                @error('especialidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label esp-label">N.º Colegiado</label>
                <input type="text" name="num_colegiado" class="form-control form-control-sm @error('num_colegiado') is-invalid @enderror"
                    placeholder="Número o N/A"
                    value="{{ old('num_colegiado', $esp?->num_colegiado) }}">
                @error('num_colegiado') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

        </div>
    </div>
</div>

{{-- Sección: Contacto --}}
<div class="esp-card mb-4">
    <div class="card-body p-4">
        <h6 class="fw-semibold text-dark mb-4 pb-2 border-bottom">
            <i class="ti ti-phone me-2 text-primary"></i>Datos de Contacto
        </h6>
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label esp-label">Teléfono del Especialista</label>
                <input type="text" name="telefono" class="form-control form-control-sm @error('telefono') is-invalid @enderror"
                    placeholder="+507 6000-0000"
                    value="{{ old('telefono', $esp?->telefono) }}">
                @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label esp-label">Correo Profesional</label>
                <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                    placeholder="especialista@clinica.com"
                    value="{{ old('email', $esp?->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

        </div>
    </div>
</div>

{{-- Sección: Acceso al Panel Especialista --}}
<div class="esp-card mb-4">
    <div class="card-body p-4">
        <h6 class="fw-semibold text-dark mb-1 pb-2 border-bottom">
            <i class="ti ti-lock me-2 text-primary"></i>Acceso al Panel Clínico
        </h6>
        <p class="text-muted small mb-3">
            El especialista podrá ingresar a su panel en <strong>/panel/login</strong> con su correo profesional y esta contraseña.
        </p>
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label esp-label">
                    Contraseña del panel
                    @if($esp) <span class="text-muted fw-normal">(dejar vacío para no cambiar)</span> @else <span class="text-danger">*</span> @endif
                </label>
                <input type="password" name="password"
                       class="form-control form-control-sm @error('password') is-invalid @enderror"
                       placeholder="Mínimo 8 caracteres"
                       {{ $esp ? '' : 'required' }}>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label esp-label">
                    Confirmar contraseña
                    @if(!$esp) <span class="text-danger">*</span> @endif
                </label>
                <input type="password" name="password_confirmation"
                       class="form-control form-control-sm"
                       placeholder="Repite la contraseña"
                       {{ $esp ? '' : 'required' }}>
            </div>

        </div>
    </div>
</div>

{{-- Sección: Firma --}}
<div class="esp-card mb-4">
    <div class="card-body p-4">
        <h6 class="fw-semibold text-dark mb-4 pb-2 border-bottom">
            <i class="ti ti-signature me-2 text-primary"></i>Firma Digital <small class="text-muted fw-normal">(Opcional — PNG)</small>
        </h6>

        @if($esp?->firma)
        <div class="mb-3">
            <p class="esp-label mb-2">Firma actual:</p>
            <img src="{{ Storage::url($esp->firma) }}" alt="Firma" class="esp-firma-preview d-block mb-2">
            <small class="text-muted">Sube una nueva imagen para reemplazarla.</small>
        </div>
        @endif

        <label class="esp-firma-upload d-block" for="firma-input">
            <input type="file" name="firma" id="firma-input" accept="image/png" onchange="previewFirma(this)">
            <i class="ti ti-upload d-block mb-1" style="font-size:1.5rem;color:#cbd5e0;"></i>
            <p class="mb-0 text-muted" style="font-size:.85rem;">
                Arrastra o haz clic para subir la firma en PNG<br>
                <small>Máximo 2 MB</small>
            </p>
        </label>
        <div id="firma-preview-container" class="mt-2" style="display:none;">
            <p class="esp-label mb-1">Vista previa:</p>
            <img id="firma-preview" src="" alt="Vista previa firma" class="esp-firma-preview">
        </div>
        @error('firma') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>
</div>
