@extends('layouts.app')

@section('title', 'Activity Log')

@push('styles')
    @vite(['resources/css/timesheet.css'])
@endpush

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="timesheet-header">
        <div class="timesheet-header-content">
            <h1 class="mb-2 heading-1-75rem">Activity Log</h1>
            <p class="mb-0 text-opacity-90">View your complete clock-in and clock-out history with details</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Activity List -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($timesheets->count())
                        <div class="activity-timeline">
                            @foreach($timesheets as $timesheet)
                                <div class="activity-item-card" data-timesheet-id="{{ $timesheet->id }}">
                                    <div class="activity-item-header" onclick="toggleActivityDetails({{ $timesheet->id }})">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="activity-icon-wrapper {{ $timesheet->clock_out_time ? 'completed' : 'active' }}">
                                                <i class="bi {{ $timesheet->clock_out_time ? 'bi-check-circle-fill' : 'bi-clock-history' }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 fw-bold">{{ $timesheet->client->company_name ?? 'Unknown Client' }}</h5>
                                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar3 me-1"></i>{{ $timesheet->work_date->format('M d, Y') }}
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ $timesheet->clock_in_time->format('h:i A') }}
                                                        @if($timesheet->clock_out_time)
                                                            - {{ $timesheet->clock_out_time->format('h:i A') }}
                                                        @else
                                                            <span class="badge bg-warning ms-2">In Progress</span>
                                                        @endif
                                                    </small>
                                                    @if($timesheet->clock_out_time)
                                                        <small class="text-success fw-bold">
                                                            <i class="bi bi-hourglass-split me-1"></i>{{ number_format($timesheet->hours_worked, 2) }}h
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="activity-toggle">
                                                <i class="bi bi-chevron-down" id="chevron-{{ $timesheet->id }}"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Expandable Details Section -->
                                    <div class="activity-details" id="details-{{ $timesheet->id }}" style="display: none;">
                                        <div class="activity-details-content">
                                            <div class="row g-3">
                                                <!-- Basic Information -->
                                                <div class="col-md-6">
                                                    <h6 class="text-muted text-uppercase small mb-3">
                                                        <i class="bi bi-info-circle me-2"></i>Basic Information
                                                    </h6>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Company:</span>
                                                        <span class="detail-value">{{ $timesheet->client->company_name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Work Date:</span>
                                                        <span class="detail-value">{{ $timesheet->work_date->format('F d, Y (l)') }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Clock In:</span>
                                                        <span class="detail-value">{{ $timesheet->clock_in_time->format('h:i A') }}</span>
                                                    </div>
                                                    @if($timesheet->clock_out_time)
                                                        <div class="detail-item">
                                                            <span class="detail-label">Clock Out:</span>
                                                            <span class="detail-value">{{ $timesheet->clock_out_time->format('h:i A') }}</span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Total Hours:</span>
                                                            <span class="detail-value fw-bold text-success">{{ number_format($timesheet->hours_worked, 2) }} hours</span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Payable Hours:</span>
                                                            <span class="detail-value">{{ number_format($timesheet->payable_hours ?? 0, 2) }} hours</span>
                                                        </div>
                                                    @else
                                                        <div class="detail-item">
                                                            <span class="detail-label">Status:</span>
                                                            <span class="badge bg-warning">Active Session</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Additional Details -->
                                                <div class="col-md-6">
                                                    <h6 class="text-muted text-uppercase small mb-3">
                                                        <i class="bi bi-file-text me-2"></i>Additional Details
                                                    </h6>
                                                    @if($timesheet->notes)
                                                        <div class="detail-item">
                                                            <span class="detail-label">Notes:</span>
                                                            <span class="detail-value">{{ $timesheet->notes }}</span>
                                                        </div>
                                                    @else
                                                        <div class="detail-item">
                                                            <span class="detail-label">Notes:</span>
                                                            <span class="detail-value text-muted">No notes provided</span>
                                                        </div>
                                                    @endif
                                                    <div class="detail-item">
                                                        <span class="detail-label">Status:</span>
                                                        @if($timesheet->status === 'approved' || $timesheet->is_approved)
                                                            <span class="badge bg-success">Approved</span>
                                                        @elseif($timesheet->status === 'completed')
                                                            <span class="badge bg-info">Completed</span>
                                                        @else
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                    </div>
                                                    @if($timesheet->approved_by && $timesheet->approved_at)
                                                        <div class="detail-item">
                                                            <span class="detail-label">Approved By:</span>
                                                            <span class="detail-value">{{ $timesheet->approvedBy->name ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Approved At:</span>
                                                            <span class="detail-value">{{ $timesheet->approved_at->format('M d, Y h:i A') }}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Job Photos -->
                                                @if($timesheet->jobPhotos->count() > 0)
                                                    <div class="col-12">
                                                        <h6 class="text-muted text-uppercase small mb-3">
                                                            <i class="bi bi-images me-2"></i>Job Photos
                                                        </h6>
                                                        <div class="row g-2">
                                                            @foreach($timesheet->jobPhotos as $photo)
                                                                @php
                                                                    $photoPath = preg_replace('#^(storage|public)/#i', '', $photo->file_path ?? '');
                                                                @endphp
                                                                <div class="col-md-3 col-sm-6">
                                                                    <div class="photo-card">
                                                                        <a href="{{ Storage::url($photoPath) }}" target="_blank" class="photo-link">
                                                                            <img src="{{ Storage::url($photoPath) }}"
                                                                                 alt="{{ $photo->photo_type }} photo"
                                                                                 class="photo-thumbnail"
                                                                                 onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                                                            <div class="photo-overlay">
                                                                                <span class="badge bg-{{ $photo->photo_type === 'before' ? 'warning' : 'success' }}">
                                                                                    {{ ucfirst($photo->photo_type) }}
                                                                                </span>
                                                                                @if($photo->status === 'approved')
                                                                                    <span class="badge bg-success ms-1">
                                                                                        <i class="bi bi-check-circle"></i>
                                                                                    </span>
                                                                                @elseif($photo->status === 'rejected')
                                                                                    <span class="badge bg-danger ms-1">
                                                                                        <i class="bi bi-x-circle"></i>
                                                                                    </span>
                                                                                @else
                                                                                    <span class="badge bg-secondary ms-1">
                                                                                        <i class="bi bi-clock"></i>
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </a>
                                                                        <div class="photo-info">
                                                                            <small class="text-muted">{{ $photo->created_at->format('M d, h:i A') }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted icon-3rem"></i>
                            <p class="text-muted mt-3 mb-0">No activity recorded yet.</p>
                            <p class="text-muted small">Your clock-in and clock-out activities will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleActivityDetails(timesheetId) {
        const detailsDiv = document.getElementById('details-' + timesheetId);
        const chevron = document.getElementById('chevron-' + timesheetId);
        const isVisible = detailsDiv.style.display !== 'none';

        if (isVisible) {
            detailsDiv.style.display = 'none';
            chevron.classList.remove('rotate');
        } else {
            detailsDiv.style.display = 'block';
            chevron.classList.add('rotate');
        }
    }

    // Close all details when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.activity-item-card')) {
            document.querySelectorAll('.activity-details').forEach(detail => {
                detail.style.display = 'none';
            });
            document.querySelectorAll('.activity-toggle i').forEach(chevron => {
                chevron.classList.remove('rotate');
            });
        }
    });
</script>

<style>
    .activity-timeline {
        padding: 1rem;
    }

    .activity-item-card {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        background: #fff;
        transition: all 0.3s ease;
    }

    .activity-item-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-color: #84c373;
    }

    .activity-item-header {
        padding: 1.25rem;
        cursor: pointer;
        user-select: none;
    }

    .activity-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .activity-icon-wrapper.completed {
        background: #d1e7dd;
        color: #0f5132;
    }

    .activity-icon-wrapper.active {
        background: #fff3cd;
        color: #856404;
    }

    .activity-toggle {
        color: #6c757d;
        transition: transform 0.3s ease;
    }

    .activity-toggle i.rotate {
        transform: rotate(180deg);
    }

    .activity-details {
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
        }
        to {
            opacity: 1;
            max-height: 1000px;
        }
    }

    .activity-details-content {
        padding: 1.5rem;
    }

    .detail-item {
        margin-bottom: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
    }

    .detail-value {
        font-size: 0.95rem;
        color: #212529;
    }

    .photo-card {
        position: relative;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .photo-link {
        display: block;
        position: relative;
        overflow: hidden;
    }

    .photo-thumbnail {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .photo-link:hover .photo-thumbnail {
        transform: scale(1.05);
    }

    .photo-overlay {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        display: flex;
        gap: 0.25rem;
    }

    .photo-info {
        padding: 0.5rem;
        background: #fff;
        text-align: center;
    }

    .timesheet-header {
        background: linear-gradient(135deg, #84c373 0%, #6ab04c 100%);
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
        color: white;
    }

    .heading-1-75rem {
        font-size: 1.75rem;
        font-weight: 700;
    }
</style>
@endpush
@endsection
