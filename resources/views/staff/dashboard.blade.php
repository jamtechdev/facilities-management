@extends('layouts.app')

@section('title', 'Staff Dashboard')

@push('styles')
    @vite(['resources/css/staff-dashboard.css', 'resources/css/clock-widget.css'])
@endpush

@section('content')
<div class="container-fluid staff-dashboard-content">
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
        <div class="col-md-6 col-lg-3">
            <div class="staff-stat-card primary">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Today's Hours</div>
                        <div class="stat-value">{{ number_format($todayHours, 1) }}</div>
                        <p class="stat-description">Hours worked today</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="staff-stat-card success">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">This Week</div>
                        <div class="stat-value">{{ number_format($weekHours, 1) }}</div>
                        <p class="stat-description">Total hours logged</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="staff-stat-card info">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">This Month</div>
                        <div class="stat-value">{{ number_format($monthHours, 1) }}</div>
                        <p class="stat-description">Monthly total</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="staff-stat-card warning">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Assigned Clients</div>
                        <div class="stat-value">{{ $assignedCompanies }}</div>
                        <p class="stat-description">Active assignments</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Today's Tasks -->
        <div class="col-lg-7">
            <div class="task-card">
                <div class="task-card-header">
                    <i class="bi bi-calendar-check"></i>
                    <h5>Today's Tasks</h5>
                </div>
                <div class="task-card-body">
                    @if($todayTasks->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table task-table">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Work Date</th>
                                        <th>Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayTasks as $task)
                                        <tr>
                                            <td>
                                                <div class="client-name">{{ $task->client?->company_name ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div class="work-date">{{ $task->work_date->format('M d, Y') }}</div>
                                            </td>
                                            <td>
                                                <span class="hours-badge">
                                                    <i class="bi bi-clock"></i>
                                                    {{ number_format($task->hours_worked, 2) }}h
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->is_approved)
                                                    <span class="status-badge-modern approved">Approved</span>
                                                @else
                                                    <span class="status-badge-modern pending">Pending</span>
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
                            <p class="empty-state-text">No tasks scheduled for today.<br>Enjoy your day off! 🎉</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="col-lg-5">
            <div class="profile-card">
                <div class="profile-card-header">
                    <i class="bi bi-person-circle"></i>
                    <h5>Your Details</h5>
                </div>
                <div class="profile-card-body">
                    <div class="profile-info-item">
                        <p class="profile-info-label">Full Name</p>
                        <p class="profile-info-value">{{ $staff->name }}</p>
                    </div>
                    <div class="profile-info-item">
                        <p class="profile-info-label">Email Address</p>
                        <p class="profile-info-value">{{ $staff->email }}</p>
                    </div>
                    <div class="profile-info-item">
                        <p class="profile-info-label">Mobile Number</p>
                        <p class="profile-info-value">{{ $staff->mobile ?? 'Not provided' }}</p>
                    </div>
                    <div class="profile-info-item">
                        <p class="profile-info-label">Hourly Rate</p>
                        <p class="profile-info-value highlight">${{ number_format($staff->hourly_rate ?? 0, 2) }}</p>
                    </div>
                    <div class="profile-info-item">
                        <p class="profile-info-label">Address</p>
                        <p class="profile-info-value">{{ $staff->address ?? 'Not provided' }}</p>
                    </div>
                </div>
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
</script>
@endpush
