@extends('layouts.app')

@section('title', 'User Dashboard')

@push('styles')
    @vite(['resources/css/clock-widget.css'])
@endpush

@section('content')
<div class="container-fluid">
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

    <div class="row g-4">
        <div class="col-md-4">
            <x-stats-card 
                icon="bi-person"
                iconColor="primary"
                label="Profile"
                :value="auth()->user()->name"
            />
        </div>

        <div class="col-md-4">
            <x-stats-card 
                icon="bi-envelope"
                iconColor="success"
                label="Email"
                :value="auth()->user()->email"
            />
        </div>

        <div class="col-md-4">
            <x-stats-card 
                icon="bi-shield-check"
                iconColor="info"
                label="Role"
                :badge="['text' => 'User', 'color' => 'primary']"
            />
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="200">Name</th>
                                <td>{{ auth()->user()->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ auth()->user()->email }}</td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td>
                                    @foreach(auth()->user()->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>Registered</th>
                                <td>{{ auth()->user()->created_at->format('F d, Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
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

