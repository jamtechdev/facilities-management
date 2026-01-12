@extends('layouts.app')

@section('title', 'Timesheet')

@push('styles')
    @vite(['resources/css/timesheet.css'])
@endpush

@section('content')
<div class="container-fluid">
    @php
        $activeTimesheet = $todayTimesheets->firstWhere('clock_out_time', null);
        $primaryAssignment = $activeTimesheet?->client ?? $assignedClients->first();
    @endphp

    <!-- Header -->
    <div class="timesheet-header">
        <div class="timesheet-header-content">
            <h1 class="mb-2 heading-1-75rem">Timesheet Management</h1>
            <p class="mb-0 text-opacity-90">Clock in, clock out, and track your work hours</p>
        </div>
    </div>

    <div id="alert-container"></div>

    <!-- Tabs -->
    <ul class="nav nav-tabs timesheet-tabs" id="timesheetTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="clock-tab" data-bs-toggle="tab" data-bs-target="#clockTab" type="button" role="tab">
                <i class="bi bi-clock-history me-2"></i>Clock In / Out
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summaryTab" type="button" role="tab">
                <i class="bi bi-bar-chart me-2"></i>Summary
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyTab" type="button" role="tab">
                <i class="bi bi-list-ul me-2"></i>Activity History
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Clock In/Out Tab -->
        <div class="tab-pane fade show active" id="clockTab" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="assignment-card-modern">
                        <div class="assignment-card-header">
                            <div class="assignment-title">Today's Assignment</div>
                            <div class="assignment-name">
                                @if($assignedClients->count() > 0)
                                    {{ $primaryAssignment->company_name ?? 'No assignment yet' }}
                                @else
                                    No companies assigned
                                @endif
                            </div>
                            <div class="assignment-address">
                                <i class="bi bi-geo-alt me-2"></i>
                                @if($assignedClients->count() > 0 && $primaryAssignment)
                                    {{ $primaryAssignment->address ?? ($primaryAssignment->city ?? 'Address not available') }}
                                @else
                                    Please contact administrator to get assigned
                                @endif
                            </div>
                        </div>
                        <div class="assignment-card-body">
                            <div class="assignment-meta-grid">
                                <div class="meta-item">
                                    <div class="meta-label">Scheduled</div>
                                    <div class="meta-value">
                                        @if($activeTimesheet)
                                            {{ $activeTimesheet->clock_in_time->format('h:i A') }}
                                        @else
                                            Flexible
                                        @endif
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Hours Today</div>
                                    <div class="meta-value">{{ number_format($todayHours, 2) }}h</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Days Worked</div>
                                    <div class="meta-value">{{ $daysWorked }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="status-card-modern">
                        <div class="assignment-card-body">
                            @if($activeTimesheet)
                                <span class="status-badge-modern active">
                                    <i class="bi bi-check-circle"></i> Clocked In
                                </span>
                                <h3 class="mb-2">{{ $activeTimesheet->client->company_name }}</h3>
                                <p class="text-muted mb-3">Since {{ $activeTimesheet->clock_in_time->format('h:i A') }}</p>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <small class="text-muted text-uppercase d-block">Elapsed</small>
                                        <strong class="text-primary" id="elapsed-time-{{ $activeTimesheet->id }}" data-clock-in-time="{{ $activeTimesheet->clock_in_time->toIso8601String() }}">
                                            @php
                                                $elapsedMinutes = max(0, now()->diffInMinutes($activeTimesheet->clock_in_time));
                                            @endphp
                                            {{ $elapsedMinutes >= 60
                                                ? number_format($elapsedMinutes / 60, 2) . 'h'
                                                : $elapsedMinutes . 'm' }}
                                        </strong>
                                    </div>
                                    <div>
                                        <small class="text-muted text-uppercase d-block">Date</small>
                                        <strong>{{ $activeTimesheet->work_date->format('M d') }}</strong>
                                    </div>
                                </div>
                            @else
                                <span class="status-badge-modern inactive">
                                    <i class="bi bi-pause-circle"></i> Offline
                                </span>
                                <h4 class="mb-2">No active shift</h4>
                                <p class="text-muted mb-0">Start your work session by clocking in.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-6">
                    <div class="clock-form-card">
                        <div class="clock-form-header">
                            <h5 class="mb-0"><i class="bi bi-play-circle me-2"></i>Clock In</h5>
                            <small>Upload a "before" photo to start your shift</small>
                        </div>
                        <div class="clock-form-body">
                            @if($todayTimesheets->whereNull('clock_out_time')->count() > 0)
                                <div class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Active Session Found</strong>
                                    <p class="mb-0 mt-2 small">You have an active clock-in session. Please clock out from your active session below before starting a new one.</p>
                                </div>
                            @endif
                            <form id="clockInForm" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="client_id" class="form-label fw-bold">Select Company</label>
                                    @if($assignedClients->count() > 0)
                                        <select class="form-select form-select-lg" id="client_id" name="client_id" required {{ $todayTimesheets->whereNull('clock_out_time')->count() > 0 ? 'disabled' : '' }}>
                                            <option value="">Choose company...</option>
                                            @foreach($assignedClients as $client)
                                                <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Select the company you want to clock in for</small>
                                        @if($todayTimesheets->whereNull('clock_out_time')->count() > 0)
                                            <small class="form-text text-danger d-block mt-1">
                                                <i class="bi bi-lock me-1"></i>Disabled - Please clock out first
                                            </small>
                                        @endif
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <strong>No companies assigned</strong>
                                            <p class="mb-0 mt-1 small">You haven't been assigned to any companies yet. Please contact your administrator to get assigned to a company.</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Before Photo</label>
                                    <label class="photo-upload-area w-100 d-block {{ $todayTimesheets->whereNull('clock_out_time')->count() > 0 ? 'opacity-50' : '' }}" for="clockInPhoto" style="{{ $todayTimesheets->whereNull('clock_out_time')->count() > 0 ? 'pointer-events: none;' : '' }}">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div id="clockInPhotoLabel" class="mt-2 fw-bold">Tap to upload shift start photo</div>
                                        <input type="file" id="clockInPhoto" name="photo" accept="image/*" capture="environment" class="d-none" required {{ $todayTimesheets->whereNull('clock_out_time')->count() > 0 ? 'disabled' : '' }}>
                                    </label>
                                    <div id="clockInPhotoPreview" class="mt-2" style="display: none;">
                                        <img id="clockInPhotoPreviewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                    </div>
                                    @if($todayTimesheets->whereNull('clock_out_time')->count() > 0)
                                        <small class="form-text text-danger d-block mt-1">
                                            <i class="bi bi-lock me-1"></i>Disabled - Please clock out first
                                        </small>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-success w-100 btn-lg" id="clockInBtn" {{ ($assignedClients->count() == 0 || $todayTimesheets->whereNull('clock_out_time')->count() > 0) ? 'disabled' : '' }}>
                                    <i class="bi bi-play-fill me-2"></i>Clock In
                                </button>
                                @if($todayTimesheets->whereNull('clock_out_time')->count() > 0)
                                    <small class="form-text text-danger d-block mt-2 text-center">
                                        <i class="bi bi-info-circle me-1"></i>Please clock out from your active session first
                                    </small>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="clock-form-card">
                        <div class="clock-form-header clock-form-header-red">
                            <h5 class="mb-0"><i class="bi bi-stop-circle me-2"></i>Active Sessions</h5>
                            <small>Clock out from your active work sessions</small>
                        </div>
                        <div class="clock-form-body">
                            @forelse($todayTimesheets->whereNull('clock_out_time') as $timesheet)
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $timesheet->client->company_name }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>Clocked in {{ $timesheet->clock_in_time->format('h:i A') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <form class="clockOutForm" data-timesheet-id="{{ $timesheet->id }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">After Photo</label>
                                            <input type="file" class="form-control clockOutPhoto" name="photo" accept="image/*" capture="environment" required data-timesheet-id="{{ $timesheet->id }}">
                                            <div class="clockOutPhotoPreview mt-2" id="clockOutPhotoPreview_{{ $timesheet->id }}" style="display: none;">
                                                <img src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Notes (optional)</label>
                                            <textarea class="form-control" name="notes" rows="2" placeholder="Any observations..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="bi bi-stop-circle me-1"></i>Clock Out
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox text-muted icon-3rem"></i>
                                    <p class="text-muted mt-3 mb-0">No active sessions</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Tab -->
        <div class="tab-pane fade" id="summaryTab" role="tabpanel">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="timesheet-stat-card">
                        <div class="stat-icon-box">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <div class="stat-value-large">{{ number_format($todayHours, 2) }}</div>
                        <div class="stat-label-small">Hours Today</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="timesheet-stat-card">
                        <div class="stat-icon-box">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-value-large">{{ number_format($weekHours, 2) }}</div>
                        <div class="stat-label-small">Hours This Week</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="timesheet-stat-card">
                        <div class="stat-icon-box">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div class="stat-value-large">{{ number_format($monthHours, 2) }}</div>
                        <div class="stat-label-small">Hours This Month</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="timesheet-stat-card">
                        <div class="stat-icon-box">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="stat-value-large">{{ $assignedClients->count() }}</div>
                        <div class="stat-label-small">Active Clients</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="assignment-card-modern">
                        <div class="assignment-card-header">
                            <div class="assignment-title">Current Assignment</div>
                            <div class="assignment-name">{{ $primaryAssignment->company_name ?? 'No assignment yet' }}</div>
                            <div class="assignment-address">
                                <i class="bi bi-geo-alt me-2"></i>
                                {{ $primaryAssignment->address ?? ($primaryAssignment->city ?? 'Address not available') }}
                            </div>
                        </div>
                        <div class="assignment-card-body">
                            <div class="assignment-meta-grid">
                                <div class="meta-item">
                                    <div class="meta-label">Scheduled</div>
                                    <div class="meta-value">
                                        @if($activeTimesheet)
                                            {{ $activeTimesheet->clock_in_time->format('h:i A') }} - {{ now()->format('h:i A') }}
                                        @else
                                            Flexible
                                        @endif
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Hours Today</div>
                                    <div class="meta-value">{{ number_format($todayHours, 2) }}h</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Days Worked</div>
                                    <div class="meta-value">{{ $daysWorked }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="status-card-modern">
                        <div class="assignment-card-body">
                            @if($activeTimesheet)
                                <span class="status-badge-modern active">
                                    <i class="bi bi-check-circle"></i> Clocked In
                                </span>
                                <h3 class="mb-2">{{ $activeTimesheet->client->company_name }}</h3>
                                <p class="text-muted mb-3">Since {{ $activeTimesheet->clock_in_time->format('h:i A') }}</p>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <small class="text-muted text-uppercase d-block">Elapsed</small>
                                        <strong class="text-primary" id="elapsed-time-summary-{{ $activeTimesheet->id }}" data-clock-in-time="{{ $activeTimesheet->clock_in_time->toIso8601String() }}">
                                            @php
                                                $elapsedMinutes = max(0, now()->diffInMinutes($activeTimesheet->clock_in_time));
                                            @endphp
                                            {{ $elapsedMinutes >= 60
                                                ? number_format($elapsedMinutes / 60, 2) . 'h'
                                                : $elapsedMinutes . 'm' }}
                                        </strong>
                                    </div>
                                    <div>
                                        <small class="text-muted text-uppercase d-block">Date</small>
                                        <strong>{{ $activeTimesheet->work_date->format('M d') }}</strong>
                                    </div>
                                </div>
                            @else
                                <span class="status-badge-modern inactive">
                                    <i class="bi bi-pause-circle"></i> Offline
                                </span>
                                <h4 class="mb-2">No active shift</h4>
                                <p class="text-muted mb-0">Select an assignment and start your next session.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div class="tab-pane fade" id="historyTab" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="clock-form-card">
                        <div class="clock-form-header">
                            <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Recent Activity</h5>
                        </div>
                        <div class="clock-form-body">
                            @forelse($timeHistory->take(10) as $entry)
                                <div class="activity-list-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $entry->client->company_name }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>{{ $entry->work_date->format('M d, Y') }}
                                                &nbsp; | &nbsp;
                                                <i class="bi bi-clock me-1"></i>
                                                {{ optional($entry->clock_in_time)->format('h:i A') ?? '—' }}
                                                -
                                                {{ optional($entry->clock_out_time)->format('h:i A') ?? 'In progress' }}
                                            </small>
                                        </div>
                                        <span class="badge bg-primary">{{ number_format($entry->hours_worked, 2) }}h</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox text-muted icon-3rem"></i>
                                    <p class="text-muted mt-3 mb-0">No recent activity</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="clock-form-card">
                        <div class="clock-form-header">
                            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Time History</h5>
                        </div>
                        <div class="clock-form-body">
                            @if($timeHistory->count())
                                <div class="table-responsive">
                                    <table class="table history-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Company</th>
                                                <th>Login</th>
                                                <th>Logout</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($timeHistory->take(15) as $entry)
                                                <tr>
                                                    <td>{{ $entry->work_date->format('M d, Y') }}</td>
                                                    <td><strong>{{ $entry->client->company_name }}</strong></td>
                                                    <td>{{ optional($entry->clock_in_time)->format('h:i A') ?? '—' }}</td>
                                                    <td>{{ optional($entry->clock_out_time)->format('h:i A') ?? '—' }}</td>
                                                    <td><strong class="text-success">{{ number_format($entry->hours_worked, 2) }}h</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox text-muted icon-3rem"></i>
                                    <p class="text-muted mt-3 mb-0">No time history found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const alertContainer = document.getElementById('alert-container');
    const clockInPhotoInput = document.getElementById('clockInPhoto');
    const clockInPhotoLabel = document.getElementById('clockInPhotoLabel');

    if (clockInPhotoInput) {
        clockInPhotoInput.addEventListener('change', function () {
            const preview = document.getElementById('clockInPhotoPreview');
            const previewImg = document.getElementById('clockInPhotoPreviewImg');

            if (this.files.length) {
                clockInPhotoLabel.textContent = this.files[0].name;

                // Show image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                clockInPhotoLabel.textContent = 'Tap to upload shift start photo';
                preview.style.display = 'none';
                previewImg.src = '';
            }
        });
    }

    // Clock In Form - Prevent duplicate listeners
    (function() {
        const clockInForm = document.getElementById('clockInForm');
        if (!clockInForm || clockInForm.dataset.listenerAttached) {
            return;
        }
        clockInForm.dataset.listenerAttached = 'true';

        let isSubmitting = false;

        clockInForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Prevent duplicate submissions
            if (isSubmitting) {
                return;
            }
            isSubmitting = true;

            if (!clockInPhotoInput || !clockInPhotoInput.files.length) {
                showAlert('warning', 'Please upload a before photo to clock in.');
                isSubmitting = false;
                return;
            }

            const formData = new FormData(this);
            const btn = document.getElementById('clockInBtn');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            try {
                const response = await fetch('{{ \App\Helpers\RouteHelper::url("timesheet.clock-in") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    // Show user-friendly error message
                    let errorMessage = data.message || 'Failed to clock in';

                    // If there's an active session, provide helpful message
                    if (data.has_active_session) {
                        errorMessage = data.message || 'You have an active session. Please clock out first.';
                        showAlert('warning', errorMessage);
                        // Scroll to active sessions section
                        setTimeout(() => {
                            const activeSessionsSection = document.querySelector('.clock-form-header-red')?.closest('.clock-form-card');
                            if (activeSessionsSection) {
                                activeSessionsSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }, 500);
                    } else {
                        showAlert('danger', errorMessage);
                    }

                    isSubmitting = false;
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                showAlert('danger', 'Failed to clock in: ' + (error.message || 'Unknown error'));
                isSubmitting = false;
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    })();

    // Clock Out Photo Preview
    document.querySelectorAll('.clockOutPhoto').forEach(photoInput => {
        photoInput.addEventListener('change', function() {
            const timesheetId = this.dataset.timesheetId;
            const preview = document.getElementById('clockOutPhotoPreview_' + timesheetId);
            const previewImg = preview?.querySelector('img');

            if (this.files.length && preview && previewImg) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            } else if (preview) {
                preview.style.display = 'none';
            }
        });
    });

    // Clock Out Forms - Prevent duplicate listeners
    document.querySelectorAll('.clockOutForm').forEach(form => {
        // Skip if listener already attached
        if (form.dataset.listenerAttached) {
            return;
        }
        form.dataset.listenerAttached = 'true';

        let isSubmitting = false;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Prevent duplicate submissions
            if (isSubmitting) {
                return;
            }
            isSubmitting = true;

            const photoInput = this.querySelector('.clockOutPhoto');
            if (!photoInput || !photoInput.files.length) {
                showAlert('warning', 'Please upload an after photo to clock out.');
                isSubmitting = false;
                return;
            }

            const formData = new FormData();
            formData.append('timesheet_id', this.dataset.timesheetId);
            formData.append('notes', this.querySelector('textarea[name="notes"]')?.value || '');
            formData.append('photo', photoInput.files[0]);

            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

            try {
                const response = await fetch('{{ \App\Helpers\RouteHelper::url("timesheet.clock-out") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert('danger', data.message || 'Failed to clock out');
                    isSubmitting = false;
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                showAlert('danger', 'Failed to clock out: ' + (error.message || 'Unknown error'));
                isSubmitting = false;
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    });

    function showAlert(type, message) {
        if (typeof showToast !== 'undefined') {
            showToast(type, message);
        } else if (typeof toastr !== 'undefined') {
            const toastType = type === 'danger' ? 'error' : type;
            toastr[toastType](message);
        } else {
            alert(message);
        }
    }

    // Real-time elapsed time update for active sessions
    function updateElapsedTime() {
        document.querySelectorAll('[id^="elapsed-time"]').forEach(element => {
            const clockInTime = element.dataset.clockInTime;

            if (clockInTime) {
                const clockIn = new Date(clockInTime);
                const now = new Date();
                const diffMs = Math.max(0, now - clockIn);
                const diffMinutes = Math.floor(diffMs / 60000);

                if (diffMinutes >= 60) {
                    const hours = (diffMinutes / 60).toFixed(2);
                    element.textContent = hours + 'h';
                } else {
                    element.textContent = diffMinutes + 'm';
                }
            }
        });
    }

    // Update elapsed time every minute if there's an active session
    @if($activeTimesheet)
        document.addEventListener('DOMContentLoaded', function() {
            // Update immediately and then every 60 seconds
            updateElapsedTime();
            setInterval(updateElapsedTime, 60000);
        });
    @endif
</script>
@endpush
@endsection
