@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
])

@php
$baseClasses = 'btn';
$variantClasses = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'success' => 'btn-success',
    'danger' => 'btn-danger',
    'warning' => 'btn-warning',
    'info' => 'btn-info',
    'light' => 'btn-light',
    'outline-primary' => 'btn-outline-primary',
];
$sizeClasses = [
    'sm' => 'btn-sm',
    'md' => '',
    'lg' => 'btn-lg',
];
@endphp

<button 
    type="{{ $type }}" 
    class="{{ $baseClasses }} {{ $variantClasses[$variant] ?? '' }} {{ $sizeClasses[$size] ?? '' }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => '']) }}
>
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
    @endif
    
    @if($icon)
        <i class="{{ $icon }} me-1"></i>
    @endif
    
    {{ $slot }}
</button>
