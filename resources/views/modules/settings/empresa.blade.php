@php
    $empresa = $empresa ?? null;
@endphp

<div class="tab-pane fade show active" id="empresa" role="tabpanel">
    <div class="card settings-card">
        <div class="card-header bg-transparent border-0">
            <h5 class="mb-0">
                <i class="ti ti-building me-2"></i>Información de la Empresa
            </h5>
        </div>
        <div class="card-body">
            <x-settings.tabs.empresa.form :empresa="$empresa" />
        </div>
    </div>
</div>
