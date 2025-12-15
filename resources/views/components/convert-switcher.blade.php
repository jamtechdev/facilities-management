@props([
    'lead',
    'canConvert' => false
])

@php
    // Check if user can convert leads - SuperAdmin or user with 'convert leads' permission
    $user = auth()->user();
    $hasConvertPermission = $user->hasRole('SuperAdmin') || $user->can('convert leads');
    $canConvertLead = $hasConvertPermission && $canConvert && !$lead->converted_to_client_id && $lead->stage == 'qualified';
@endphp

@if($canConvertLead)
<div class="convert-switcher-wrapper mb-3">
    <div class="card border-warning">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">
                        <i class="bi bi-arrow-repeat me-2 text-warning"></i>Ready to Convert
                    </h6>
                    <p class="mb-0 small text-muted">
                        This lead is qualified and ready to be converted to a client. All data will be migrated.
                    </p>
                </div>
                <button type="button" class="btn btn-success btn-lg" id="convertToClientBtn" data-lead-id="{{ $lead->id }}">
                    <i class="bi bi-arrow-right-circle me-2"></i>Convert to Client
                </button>
            </div>
        </div>
    </div>
</div>
@elseif($lead->converted_to_client_id)
<div class="convert-switcher-wrapper mb-3">
    <div class="card border-success">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">
                        <i class="bi bi-check-circle me-2 text-success"></i>Converted to Client
                    </h6>
                    <p class="mb-0 small text-muted">
                        This lead has been converted to a client.
                        @if($lead->convertedToClient)
                            <a href="{{ route('admin.clients.show', $lead->convertedToClient) }}" class="text-decoration-none">
                                View Client <i class="bi bi-arrow-right"></i>
                            </a>
                        @endif
                    </p>
                </div>
                <span class="badge bg-success">Converted</span>
            </div>
        </div>
    </div>
</div>
@endif

