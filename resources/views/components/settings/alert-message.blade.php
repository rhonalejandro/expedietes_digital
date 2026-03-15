@props([
    'type' => 'success',
    'message' => null,
    'dismissible' => true,
])

@if($message || $slot->isNotEmpty())
    @php
        $alertClasses = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
        ];
        
        $icons = [
            'success' => '',
            'error' => 'ti-x',
            'warning' => 'ti-alert-triangle',
            'info' => 'ti-info-circle',
        ];
    @endphp

    <div 
        class="alert {{ $alertClasses[$type] ?? 'alert-success' }} alert-custom" 
        role="alert"
        {{ $attributes->merge(['class' => '']) }}
    >
        @if($dismissible)
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        @endif
        
        <i class="{{ $icons[$type] ?? '' }} me-2"></i>
        
        {{ $message ?? $slot }}
    </div>
@endif
