@props([
    'name',
    'label' => null,
    'checked' => false,
    'disabled' => false,
    'value' => 1,
    'id' => null,
])

<div class="form-check form-switch">
    <input 
        class="form-check-input" 
        type="checkbox" 
        id="{{ $id ?? $name }}" 
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => '']) }}
    >
    
    @if($label)
        <label class="form-check-label" for="{{ $id ?? $name }}">
            {{ $label }}
        </label>
    @endif
</div>
