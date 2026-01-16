@extends('layouts.app')

@section('title', 'Our Staff')

@push('styles')
    @vite(['resources/css/profile.css', 'resources/css/client-dashboard.css', 'resources/css/entity-details.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="d-flex align-items-center gap-4">
                {{-- <div class="profile-avatar avatar-large">
                    <i class="bi bi-people icon-2-5rem"></i>
                </div> --}}
                 <div class="profile-avatar">
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        <i class="bi bi-person-lines-fill icon-2-5rem"></i>
                    @endif
                </div>
                <div>
                    <h1 class="client-greeting mb-2 heading-2-5rem">
                        Our Staff
                    </h1>
                    <p class="client-subtitle mb-1 subtitle-1-25rem">
                        {{ $client->company_name }}
                    </p>
                    <p class="client-subtitle opacity-80">
                        View all staff members assigned to your account and their activities
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Cards -->
    <div class="row g-4 mt-4">
        @if($staff->count() > 0)
            @foreach($staff as $staffMember)
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-header">
                            <div class="d-flex align-items-center gap-3">
                                <div class="profile-avatar avatar-medium">
                                    {{ strtoupper(substr($staffMember->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $staffMember->name }}</h5>
                                    <p class="mb-0 text-muted small">{{ $staffMember->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="service-card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Mobile</small>
                                    <strong>{{ $staffMember->mobile ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="badge bg-{{ $staffMember->is_active ? 'success' : 'secondary' }}">
                                        {{ $staffMember->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                @if($staffMember->pivot)
                                    <div class="col-6">
                                        <small class="text-muted d-block">Weekly Hours</small>
                                        <strong>{{ $staffMember->pivot->assigned_weekly_hours ?? '-' }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Monthly Hours</small>
                                        <strong>{{ $staffMember->pivot->assigned_monthly_hours ?? '-' }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="d-grid">
                                <a href="{{ \App\Helpers\RouteHelper::url('staff.show', $staffMember) }}" class="btn btn-primary">
                                    <i class="bi bi-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-people icon-48px empty-state-icon-medium"></i>
                    <h4 class="mt-3 text-muted">No Staff Assigned</h4>
                    <p class="text-muted">No staff members have been assigned to your account yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
