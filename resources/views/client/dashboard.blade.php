@extends('layouts.app')

@section('title', 'Client Dashboard')

@push('styles')
    @vite(['resources/css/client-dashboard.css', 'resources/css/clock-widget.css'])
@endpush

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
                        <div class="stat-label">Profile</div>
                        <div class="stat-value">✔</div>
                        <p class="stat-description">Client profile active</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-person-circle"></i>
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
            <div class="feedback-card">
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
            </div>

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
