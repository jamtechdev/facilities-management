@extends('layouts.app')

@section('title', 'Client Dashboard')

@push('styles')
    @vite(['resources/css/client-dashboard.css', 'resources/css/clock-widget.css', 'resources/css/entity-details.css'])
@endpush

@push('scripts')
    @vite(['resources/js/image-modal.js'])
@endpush

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid client-dashboard-content">
    <!-- Real-time Clock Widget -->
    <div class="clock-widget">
        <div class="clock-content">
            <!-- Welcome Message Section -->
            <div class="clock-welcome-section">
                <div class="clock-welcome-icon">
                    <i class="bi bi-hand-thumbs-up"></i>
                </div>
                <div class="clock-welcome-text">
                    <div class="clock-welcome-greeting">Welcome Back</div>
                    <div class="clock-welcome-name">{{ auth()->user()->name }}</div>
                </div>
            </div>

            <!-- Clock Section (Right Side) -->
            <div class="clock-time-section">
                <div class="clock-icon-wrapper">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="clock-time-display">
                    <div class="clock-time" id="clock-time">--:--:--</div>
                    <div class="clock-date" id="clock-date">-- --, ----</div>
                </div>
                <div class="clock-day" id="clock-day">----</div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="client-stat-card primary">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Recent Services</div>
                        <div class="stat-value">{{ $recentServices->count() }}</div>
                        <p class="stat-description">Last 10 service records</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="client-stat-card success">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Feedback Received</div>
                        <div class="stat-value">{{ $recentFeedback->count() }}</div>
                        <p class="stat-description">Last 5 feedback entries</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="client-stat-card info">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Assigned Staff</div>
                        <div class="stat-value">{{ $staff->count() }}</div>
                        <p class="stat-description">Staff members assigned to you</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Recent Services -->
        <div class="col-lg-7">
            <div class="service-card">
                <div class="service-card-header">
                    <i class="bi bi-calendar-check"></i>
                    <h5>Recent Services</h5>
                </div>
                <div class="service-card-body">
                    @if($recentServices->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table service-table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Work Date</th>
                                        <th>Hours</th>
                                        <th>Photos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentServices as $service)
                                        <tr>
                                            <td>{{ $service->staff?->name ?? 'N/A' }}</td>
                                            <td>{{ $service->work_date->format('M d, Y') }}</td>
                                            <td>{{ number_format($service->hours_worked, 2) }}h</td>
                                            <td>
                                                @if($service->jobPhotos->isNotEmpty())
                                                    <span class="badge bg-success">{{ $service->jobPhotos->count() }} Photos</span>
                                                @else
                                                    <span class="badge bg-secondary">No Photos</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <p class="empty-state-text">No recent services found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Feedback Card -->
        <div class="col-lg-5">
            {{-- <div class="feedback-card">
                <div class="feedback-card-header">
                    <i class="bi bi-chat-square-text"></i>
                    <h5>Recent Feedback</h5>
                </div>
                <div class="feedback-card-body">
                    @if($recentFeedback->isNotEmpty())
                        <ul class="list-group">
                            @foreach($recentFeedback as $feedback)
                                <li class="list-group-item">
                                    <p class="feedback-text">"{{ $feedback->comments }}"</p>
                                    <small class="text-muted">Submitted on {{ $feedback->created_at->format('M d, Y') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-chat"></i>
                            </div>
                            <p class="empty-state-text">No feedback received yet.</p>
                        </div>
                    @endif
                </div>
            </div> --}}

            <!-- Profile Card -->
            <div class="profile-card mt-4">
                <div class="profile-card-header">
                    <i class="bi bi-person-circle"></i>
                    <h5>Your Details</h5>
                </div>
                <div class="profile-card-body">
                    <div class="profile-info-item">
                        <p class="profile-info-label">Company Name</p>
                        <p class="profile-info-value">{{ $client->company_name ?? 'N/A' }}</p>
                    </div>
                    <div class="profile-info-item">
                        <p class="profile-info-label">Contact Person</p>
                        <p class="profile-info-value">{{ $client->name }}</p>
                    </div>
                    <div class="profile-info-item">
                        <p class="profile-info-label">Email Address</p>
                        <p class="profile-info-value">{{ $client->email }}</p>
                    </div>
                    <div class="profile-info-item">
                        <p class="profile-info-label">Phone</p>
                        <p class="profile-info-value">{{ $client->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="profile-info-item">
                        <p class="profile-info-label">Address</p>
                        <p class="profile-info-value">{{ $client->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Staff Section -->
    {{-- <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="service-card">
                <div class="service-card-header">
                    <i class="bi bi-people"></i>
                    <h5>Our Staff ({{ $staff->count() }})</h5>
                </div>
                <div class="service-card-body">
                    @if($staff->isNotEmpty())
                        @foreach($staff as $staffMember)
                            <div class="staff-details-section mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                                <!-- Staff Header -->
                                <div class="mb-3">
                                    <h6 class="mb-2">
                                        <i class="bi bi-person-circle me-2"></i>{{ $staffMember->name }}
                                    </h6>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @if($staffMember->email)
                                            <small class="text-muted">
                                                <i class="bi bi-envelope me-1"></i>{{ $staffMember->email }}
                                            </small>
                                        @endif
                                        @if($staffMember->mobile)
                                            <small class="text-muted">
                                                <i class="bi bi-telephone me-1"></i>{{ $staffMember->mobile }}
                                            </small>
                                        @endif
                                        <span class="badge bg-{{ $staffMember->is_active ? 'success' : 'secondary' }}">
                                            {{ $staffMember->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Staff Information Tabs -->
                                <ul class="nav nav-tabs mb-3" id="staffTabs{{ $staffMember->id }}" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="info-tab-{{ $staffMember->id }}" data-bs-toggle="tab" data-bs-target="#info-{{ $staffMember->id }}" type="button" role="tab">
                                            <i class="bi bi-info-circle me-2"></i>Information
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="timesheets-tab-{{ $staffMember->id }}" data-bs-toggle="tab" data-bs-target="#timesheets-{{ $staffMember->id }}" type="button" role="tab">
                                            <i class="bi bi-clock-history me-2"></i>Timesheets <span class="badge bg-primary ms-2">{{ $staffMember->timesheets->count() }}</span>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="photos-tab-{{ $staffMember->id }}" data-bs-toggle="tab" data-bs-target="#photos-{{ $staffMember->id }}" type="button" role="tab">
                                            <i class="bi bi-camera me-2"></i>Job Photos <span class="badge bg-primary ms-2">{{ $staffMember->jobPhotos->count() }}</span>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="documents-tab-{{ $staffMember->id }}" data-bs-toggle="tab" data-bs-target="#documents-{{ $staffMember->id }}" type="button" role="tab">
                                            <i class="bi bi-file-earmark me-2"></i>Documents <span class="badge bg-primary ms-2">{{ $staffMember->documents->count() }}</span>
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content" id="staffTabsContent{{ $staffMember->id }}">
                                    <!-- Information Tab -->
                                    <div class="tab-pane fade show active" id="info-{{ $staffMember->id }}" role="tabpanel">
                                        <div class="row g-3">
                                            <!-- Personal Information -->
                                            <div class="col-12">
                                                <h6 class="mb-3 text-muted fw-bold">
                                                    <i class="bi bi-person-circle me-2"></i>Personal Information
                                                </h6>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <x-info-card label="Name" :value="$staffMember->name" />
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <x-info-card label="Email" :value="$staffMember->email ?? '-'" :link="$staffMember->email ? 'mailto:' . $staffMember->email : null" />
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <x-info-card label="Mobile" :value="$staffMember->mobile ?? '-'" :link="$staffMember->mobile ? 'tel:' . $staffMember->mobile : null" />
                                            </div>
                                            @if($staffMember->address)
                                                <div class="col-12">
                                                    <x-info-card label="Address" :value="$staffMember->address" />
                                                </div>
                                            @endif

                                            <!-- Work Details -->
                                            <div class="col-12 mt-4">
                                                <h6 class="mb-3 text-muted fw-bold">
                                                    <i class="bi bi-briefcase me-2"></i>Work Details
                                                </h6>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <x-info-card label="Hourly Rate" :value="$staffMember->hourly_rate ? '£' . number_format($staffMember->hourly_rate, 2) : '-'" />
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <x-info-card label="Assigned Weekly Hours (To You)" :value="$staffMember->pivot->assigned_weekly_hours ? $staffMember->pivot->assigned_weekly_hours . ' hours' : '-'" />
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <x-info-card label="Assigned Monthly Hours (To You)" :value="$staffMember->pivot->assigned_monthly_hours ? $staffMember->pivot->assigned_monthly_hours . ' hours' : '-'" />
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <x-info-card
                                                    label="Assignment Status"
                                                    :badge="$staffMember->pivot->is_active ? 'Active' : 'Inactive'"
                                                    :badgeColor="$staffMember->pivot->is_active ? 'success' : 'secondary'" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timesheets Tab -->
                                    <div class="tab-pane fade" id="timesheets-{{ $staffMember->id }}" role="tabpanel">
                                        @if($staffMember->timesheets->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Clock In</th>
                                                            <th>Clock Out</th>
                                                            <th>Hours</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($staffMember->timesheets->sortByDesc('clock_in_time') as $timesheet)
                                                            <tr>
                                                                <td>{{ $timesheet->clock_in_time->format('M d, Y') }}</td>
                                                                <td>{{ $timesheet->clock_in_time->format('h:i A') }}</td>
                                                                <td>{{ $timesheet->clock_out_time ? $timesheet->clock_out_time->format('h:i A') : '-' }}</td>
                                                                <td>
                                                                    @if($timesheet->clock_out_time && $timesheet->hours_worked)
                                                                        {{ number_format($timesheet->hours_worked, 2) }} hrs
                                                                    @elseif($timesheet->clock_out_time)
                                                                        {{ number_format($timesheet->clock_in_time->diffInHours($timesheet->clock_out_time), 2) }} hrs
                                                                    @else
                                                                        <span class="badge bg-warning">In Progress</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($timesheet->is_approved)
                                                                        <span class="badge bg-success">Approved</span>
                                                                    @else
                                                                        <span class="badge bg-warning">Pending</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="bi bi-clock-history icon-48px empty-state-icon-medium"></i>
                                                <p class="text-muted mt-3">No timesheets recorded yet for this client</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Job Photos Tab -->
                                    <div class="tab-pane fade" id="photos-{{ $staffMember->id }}" role="tabpanel">
                                        @if($staffMember->jobPhotos->count() > 0)
                                            <div class="row g-3">
                                                @foreach($staffMember->jobPhotos as $photo)
                                                    @php
                                                        $photoPath = preg_replace('#^(storage|public)/#i', '', $photo->file_path ?? '');
                                                        $imageUrl = Storage::url($photoPath);
                                                    @endphp
                                                    <div class="col-md-4 col-lg-3">
                                                        <div class="document-item">
                                                            <div class="text-center">
                                                                <img src="{{ $imageUrl }}"
                                                                     alt="Job Photo"
                                                                     class="img-thumbnail"
                                                                     style="max-height: 200px; cursor: pointer; width: 100%; object-fit: cover;"
                                                                     onclick="openImageModal('{{ $imageUrl }}', 'Job Photo - {{ $staffMember->name }}')"
                                                                     onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                                                <p class="mt-2 mb-0 small">
                                                                    <span class="badge bg-{{ $photo->photo_type == 'before' ? 'warning' : 'success' }} me-1">
                                                                        {{ ucfirst($photo->photo_type) }}
                                                                    </span>
                                                                    @if($photo->status == 'approved')
                                                                        <span class="badge bg-success me-1">
                                                                            <i class="bi bi-check-circle"></i> Approved
                                                                        </span>
                                                                    @elseif($photo->status == 'rejected')
                                                                        <span class="badge bg-danger me-1">
                                                                            <i class="bi bi-x-circle"></i> Rejected
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-warning me-1">
                                                                            <i class="bi bi-clock"></i> Pending
                                                                        </span>
                                                                    @endif
                                                                    <br>
                                                                    <small class="text-muted">{{ $photo->created_at->format('M d, Y') }}</small>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="bi bi-camera icon-48px empty-state-icon-medium"></i>
                                                <p class="text-muted mt-3">No job photos uploaded yet for this client</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Documents Tab -->
                                    <div class="tab-pane fade" id="documents-{{ $staffMember->id }}" role="tabpanel">
                                        @if($staffMember->documents->count() > 0)
                                            @foreach($staffMember->documents as $document)
                                                <div class="document-item">
                                                    <div>
                                                        <i class="bi bi-file-earmark me-2"></i>
                                                        <strong>{{ $document->name }}</strong>
                                                        <span class="badge bg-secondary ms-2">{{ ucfirst($document->document_type) }}</span>
                                                        <small class="text-muted ms-2">{{ $document->created_at->format('M d, Y') }}</small>
                                                        @if($document->uploadedBy)
                                                            <small class="text-muted ms-2">by {{ $document->uploadedBy->name }}</small>
                                                        @endif
                                                    </div>
                                                    <a href="{{ asset('storage/' . $document->file_path) }}" class="btn btn-sm btn-outline-primary" download target="_blank">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-5">
                                                <i class="bi bi-file-earmark icon-48px empty-state-icon-medium"></i>
                                                <p class="text-muted mt-3">No documents uploaded yet</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <p class="empty-state-text">No staff assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div> --}}
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Job Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Job Photo" class="img-fluid modal-image" onerror="this.src='/Image-not-found.png'; this.onerror=null;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Real-time Clock with Running Seconds
    function updateClock() {
        const now = new Date();

        // Time
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        // Date
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        const dayName = days[now.getDay()];
        const monthName = months[now.getMonth()];
        const day = now.getDate();
        const year = now.getFullYear();

        // Update elements
        const timeElement = document.getElementById('clock-time');
        const dateElement = document.getElementById('clock-date');
        const dayElement = document.getElementById('clock-day');

        if (timeElement) timeElement.textContent = `${hours}:${minutes}:${seconds}`;
        if (dateElement) dateElement.textContent = `${monthName} ${day}, ${year}`;
        if (dayElement) dayElement.textContent = dayName;
    }

    // Update immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);

    // Image Modal Function
    function openImageModal(imageSrc, title) {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModalLabel').textContent = title || 'Job Photo';
        modal.show();
    }
</script>
@endpush
