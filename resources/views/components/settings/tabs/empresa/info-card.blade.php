@props([
    'title' => 'Información',
    'items' => [],
    'icon' => 'ti ti-building',
])

<div class="info-card">
    <div class="card-header bg-transparent border-0">
        <h5 class="mb-0">
            <i class="{{ $icon }} me-2"></i>
            {{ $title }}
        </h5>
    </div>
    
    <div class="card-body">
        @if(count($items) > 0)
            <dl class="row mb-0">
                @foreach($items as $key => $value)
                    <dt class="col-sm-4 text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                    <dd class="col-sm-8">{{ $value }}</dd>
                @endforeach
            </dl>
        @else
            <p class="text-muted mb-0">No hay información disponible</p>
        @endif
    </div>
</div>
