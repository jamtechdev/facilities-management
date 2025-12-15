@extends('layouts.app')

@section('title', 'SuperAdmin Dashboard')

@push('styles')
    @vite(['resources/css/superadmin-dashboard.css'])
@endpush

@section('content')
<div class="container-fluid superadmin-dashboard-content">
    <!-- Welcome Header -->
    <div class="superadmin-dashboard-header">
        <div class="superadmin-dashboard-header-content">
            <h1 class="superadmin-greeting">Welcome back, {{ auth()->user()->name }}! 👋</h1>
            <p class="superadmin-subtitle">SuperAdmin Dashboard - Full system control and management</p>
            <div class="superadmin-status-badge">
                <i></i>
                <span>Super Admin Dashboard</span>
            </div>
        </div>
    </div>

    <!-- Primary Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Total Leads</div>
                        <div class="stat-value">{{ number_format($stats['total_leads']) }}</div>
                        <p class="stat-description">{{ number_format($stats['new_leads']) }} new today</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Active Clients</div>
                        <div class="stat-value">{{ number_format($stats['total_clients']) }}</div>
                        <p class="stat-description">Currently active</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Active Staff</div>
                        <div class="stat-value">{{ number_format($stats['total_staff']) }}</div>
                        <p class="stat-description">On duty</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">£{{ number_format($stats['revenue'], 0) }}</div>
                        <p class="stat-description">{{ number_format($stats['total_invoices']) }} invoices</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-currency-pound"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Qualified Leads</div>
                        <div class="stat-value">{{ number_format($stats['qualified_leads']) }}</div>
                        <a href="{{ route('admin.leads.index', ['stage' => 'qualified']) }}" class="stat-link">
                            View all <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">New Leads Today</div>
                        <div class="stat-value">{{ number_format($stats['new_leads']) }}</div>
                        <a href="{{ route('admin.leads.index', ['stage' => 'new_lead']) }}" class="stat-link">
                            View all <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
                        <p class="stat-description">{{ number_format($stats['total_admins']) }} Admins</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-person-gear"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Total Invoices</div>
                        <div class="stat-value">{{ number_format($stats['total_invoices']) }}</div>
                        <a href="{{ route('admin.invoices.index') }}" class="stat-link">
                            View all <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow-up Reminders and Recent Activity -->
    <div class="row g-4">
        <!-- Automated Follow-up Reminders -->
        <div class="col-lg-6 col-md-12">
            <div class="activity-card">
                <div class="activity-card-header">
                    <h5>
                        <i class="bi bi-bell-fill"></i>
                        Automated Follow-up Reminders
                    </h5>
                    @if($followUpReminders->count() > 0)
                        <span class="badge" style="background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%); color: white;">{{ $followUpReminders->count() }}</span>
                    @endif
                </div>
                <div class="activity-card-body">
                    @if($followUpReminders->count() > 0)
                        @foreach($followUpReminders->take(8) as $reminder)
                            @php
                                $isOverdue = $reminder->due_date->isPast();
                                $daysUntil = $reminder->due_date->diffInDays(\Carbon\Carbon::now(), false);
                                $daysUntilRounded = round(abs($daysUntil));
                                $itemClass = $isOverdue ? 'activity-item overdue' : ($daysUntil <= 3 ? 'activity-item urgent' : 'activity-item');
                            @endphp
                            <div class="{{ $itemClass }}">
                                <div class="item-title">
                                    <a href="{{ route('admin.leads.show', $reminder->lead) }}">
                                        <i class="bi bi-person me-2"></i>{{ $reminder->lead->name }}
                                    </a>
                                </div>
                                <div class="item-description">
                                    {{ Str::limit($reminder->suggestion, 70) }}
                                </div>
                                <div class="item-meta">
                                    <div class="item-meta-item">
                                        <i class="bi bi-calendar-event"></i>
                                        <span>Day {{ $reminder->reminder_day }}</span>
                                    </div>
                                    <div class="item-meta-item">
                                        <i class="bi bi-clock"></i>
                                        <span>{{ $reminder->due_date->format('M d, Y') }}</span>
                                    </div>
                                    @if($isOverdue)
                                        <span class="badge bg-danger activity-badge">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $daysUntilRounded }} days overdue
                                        </span>
                                    @elseif($daysUntil <= 3)
                                        <span class="badge bg-warning activity-badge text-dark">
                                            <i class="bi bi-clock-history me-1"></i>{{ $daysUntilRounded }} days left
                                        </span>
                                    @else
                                        <span class="badge activity-badge" style="background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%); color: white;">
                                            <i class="bi bi-check-circle me-1"></i>{{ $daysUntilRounded }} days left
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @if($followUpReminders->count() > 8)
                            <div class="text-center pt-2">
                                <a href="{{ route('admin.leads.index') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%); color: white; border: none;">
                                    <i class="bi bi-arrow-right me-1"></i>View all reminders ({{ $followUpReminders->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <p class="empty-state-text">No upcoming follow-up reminders</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6 col-md-12">
            <div class="activity-card">
                <div class="activity-card-header">
                    <h5>
                        <i class="bi bi-clock-history"></i>
                        Recent Activity
                    </h5>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%); color: white; border: none;">
                        View All
                    </a>
                </div>
                <div class="activity-card-body">
                    @if($recentActivity->count() > 0)
                        @foreach($recentActivity->take(8) as $activity)
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="item-title flex-grow-1">
                                        <a href="{{ route('admin.leads.show', $activity) }}">
                                            <i class="bi bi-person me-2"></i>{{ $activity->name }}
                                        </a>
                                    </div>
                                    <span class="badge activity-badge" style="background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%); color: white;">
                                        {{ ucfirst(str_replace('_', ' ', $activity->stage)) }}
                                    </span>
                                </div>
                                <div class="item-description">
                                    <i class="bi bi-building me-2"></i>{{ $activity->company ?? 'No Company' }}
                                </div>
                                <div class="item-meta">
                                    <div class="item-meta-item">
                                        <i class="bi bi-clock"></i>
                                        <span>{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($activity->assignedStaff)
                                        <div class="item-meta-item">
                                            <i class="bi bi-person"></i>
                                            <span>{{ $activity->assignedStaff->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <p class="empty-state-text">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

