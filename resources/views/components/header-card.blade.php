@props([
    'title',
    'subtitle' => null,
    'email' => null,
    'phone' => null,
    'company' => null,
    'contactPerson' => null,
    'type' => 'lead' // 'lead' or 'client'
])

<div class="entity-header-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">{{ $title }}</h2>
            @if($type === 'lead')
                @if($company)
                    <p class="mb-1 opacity-90">
                        <i class="bi bi-building me-2"></i>{{ $company }}
                    </p>
                @endif
            @else
                @if($contactPerson)
                    <p class="mb-1 opacity-90">
                        <i class="bi bi-person me-2"></i>{{ $contactPerson }}
                    </p>
                @endif
            @endif
            <p class="mb-0 opacity-75">
                @if($email)
                    <i class="bi bi-envelope me-2"></i>{{ $email }}
                @endif
                @if($phone)
                    <span class="ms-3"><i class="bi bi-telephone me-2"></i>{{ $phone }}</span>
                @endif
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            {{ $actions ?? '' }}
        </div>
    </div>
</div>

