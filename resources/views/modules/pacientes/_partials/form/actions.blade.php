{{--
    Partial compartido por create y edit.
    Variables esperadas:
      - $cancelRoute  (string) — URL del botón cancelar
      - $submitLabel  (string) — Texto del botón submit (opcional)
--}}

<div class="card border-0 pac-detail-card">
    <div class="card-body">
        <h6 class="pac-section-title mb-3">Acciones</h6>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i>
                {{ $submitLabel ?? 'Guardar' }}
            </button>

            <a href="{{ $cancelRoute }}" class="btn btn-light">
                <i class="ti ti-x me-1"></i>Cancelar
            </a>
        </div>

        <hr class="my-3">

        <p class="text-muted mb-0" style="font-size: var(--ki-font-size-xs);">
            Los campos marcados con <span class="text-danger">*</span> son obligatorios.
        </p>
    </div>
</div>
