@props([
    'tabs' => [],
    'activeTab' => null,
])

<div class="settings-nav-container">
    <ul class="nav flex-column settings-nav" role="tablist">
        @foreach($tabs as $tab)
            <li class="nav-item" role="presentation">
                <button 
                    class="nav-link {{ ($activeTab === $tab['id']) ? 'active' : '' }}"
                    id="{{ $tab['id'] }}-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#{{ $tab['id'] }}"
                    type="button"
                    role="tab"
                >
                    @if(isset($tab['icon']))
                        <i class="{{ $tab['icon'] }}"></i>
                    @endif
                    
                    {{ $tab['label'] }}
                    
                    @if(isset($tab['badge']))
                        <span class="badge bg-primary ms-2">
                            {{ $tab['badge'] }}
                        </span>
                    @endif
                </button>
            </li>
        @endforeach
    </ul>
</div>
