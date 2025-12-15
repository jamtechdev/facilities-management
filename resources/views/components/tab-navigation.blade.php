@props([
    'tabs' => [],
    'id' => 'entityTabs'
])

<ul class="nav nav-tabs entity-tabs" id="{{ $id }}" role="tablist">
    @foreach($tabs as $tab)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                    id="{{ $tab['id'] }}-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#{{ $tab['id'] }}" 
                    type="button" 
                    role="tab">
                @if(isset($tab['icon']))
                    <i class="bi {{ $tab['icon'] }} me-2"></i>
                @endif
                {{ $tab['label'] }}
                @if(isset($tab['badge']) && $tab['badge'] > 0)
                    <span class="badge bg-primary ms-2">{{ $tab['badge'] }}</span>
                @endif
            </button>
        </li>
    @endforeach
</ul>

