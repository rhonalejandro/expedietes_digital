<div class="card border-0 pac-detail-card">
    <div class="card-body">
        <h6 class="pac-section-title mb-3">Casos Clínicos</h6>

        @if ($paciente->casos->isNotEmpty())
            <ul class="list-unstyled mb-0">
                @foreach ($paciente->casos as $caso)
                    <li class="pac-caso-item py-2 border-bottom">
                        <p class="mb-0 fw-medium text-dark">
                            {{ $caso->descripcion ?? ('Caso #' . $caso->id) }}
                        </p>
                        <small class="text-muted">{{ $caso->created_at->format('d/m/Y') }}</small>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-4 pac-empty-state">
                <i class="ti ti-folder-off d-block mb-2" style="font-size: 1.75rem;"></i>
                <p class="mb-0" style="font-size: var(--ki-font-size-sm);">Sin casos registrados</p>
                {{-- TODO: agregar enlace a crear caso cuando exista el módulo --}}
            </div>
        @endif
    </div>
</div>
