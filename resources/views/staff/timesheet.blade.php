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
            <h1 class="mb-2" style="font-size: 1.75rem; font-weight: 700;">Timesheet Management</h1>
            <p class="mb-0" style="opacity: 0.9;">Clock in, clock out, and track your work hours</p>
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
                                        <strong class="text-primary">
                                            {{ now()->diffInMinutes($activeTimesheet->clock_in_time) >= 60 
                                                ? number_format(now()->diffInMinutes($activeTimesheet->clock_in_time) / 60, 2) . 'h' 
                                                : now()->diffInMinutes($activeTimesheet->clock_in_time) . 'm' }}
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
                            <form id="clockInForm" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="client_id" class="form-label fw-bold">Select Company</label>
                                    <select class="form-select form-select-lg" id="client_id" name="client_id" required>
                                        <option value="">Choose company...</option>
                                        @foreach($assignedClients as $client)
                                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Before Photo</label>
                                    <label class="photo-upload-area w-100 d-block" for="clockInPhoto">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div id="clockInPhotoLabel" class="mt-2 fw-bold">Tap to upload shift start photo</div>
                                        <input type="file" id="clockInPhoto" name="photo" accept="image/*" capture="environment" class="d-none" required>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-success w-100 btn-lg" id="clockInBtn">
                                    <i class="bi bi-play-fill me-2"></i>Clock In
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="clock-form-card">
                        <div class="clock-form-header" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);">
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
                                            <input type="file" class="form-control clockOutPhoto" name="photo" accept="image/*" capture="environment" required>
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
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
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
                                        <strong class="text-primary">
                                            {{ now()->diffInMinutes($activeTimesheet->clock_in_time) >= 60 
                                                ? number_format(now()->diffInMinutes($activeTimesheet->clock_in_time) / 60, 2) . 'h' 
                                                : now()->diffInMinutes($activeTimesheet->clock_in_time) . 'm' }}
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
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
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
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
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
            if (this.files.length) {
                clockInPhotoLabel.textContent = this.files[0].name;
            } else {
                clockInPhotoLabel.textContent = 'Tap to upload shift start photo';
            }
        });
    }

    document.getElementById('clockInForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!clockInPhotoInput.files.length) {
            showAlert('warning', 'Please upload a before photo to clock in.');
            return;
        }
        const formData = new FormData(this);
        const btn = document.getElementById('clockInBtn');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        try {
            const response = await fetch('{{ route("staff.timesheet.clock-in") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            } else {
                showAlert('danger', data.message);
            }
        } catch (error) {
            showAlert('danger', 'Failed to clock in: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    document.querySelectorAll('.clockOutForm').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const photoInput = this.querySelector('.clockOutPhoto');
            if (!photoInput.files.length) {
                showAlert('warning', 'Please upload an after photo to clock out.');
                return;
            }

            const formData = new FormData();
            formData.append('timesheet_id', this.dataset.timesheetId);
            formData.append('notes', this.querySelector('textarea[name="notes"]').value);
            formData.append('photo', photoInput.files[0]);

            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';

            try {
                const response = await fetch('{{ route("staff.timesheet.clock-out") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert('danger', data.message);
                }
            } catch (error) {
                showAlert('danger', 'Failed to clock out: ' + error.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    });

    function showAlert(type, message) {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }
</script>
@endpush
@endsection
