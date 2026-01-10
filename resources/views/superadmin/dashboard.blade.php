@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@push('styles')
    @vite(['resources/css/utilities.css', 'resources/css/admin-dashboard.css', 'resources/css/clock-widget.css'])
@endpush

@section('content')
<div class="container-fluid admin-dashboard-content">
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

    <!-- Primary Stats Cards -->
    <div class="row g-4 mb-4">
        @can('view dashboard leads card')
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Total Leads</div>
                        <div class="stat-value">{{ number_format($stats['total_leads'] ?? 0) }}</div>
                        <p class="stat-description">{{ number_format($stats['new_leads'] ?? 0) }} new today</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view dashboard clients card')
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Active Clients</div>
                        <div class="stat-value">{{ number_format($stats['total_clients'] ?? 0) }}</div>
                        <p class="stat-description">Currently active</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view dashboard staff card')
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Active Staff</div>
                        <div class="stat-value">{{ number_format($stats['total_staff'] ?? 0) }}</div>
                        <p class="stat-description">On duty</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view dashboard revenue card')
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">£{{ number_format($stats['revenue'] ?? 0, 0) }}</div>
                        <p class="stat-description">{{ number_format($stats['total_invoices'] ?? 0) }} invoices</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-currency-pound"></i>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Secondary Stats Row -->
    <div class="row g-4 mb-4">
        @can('view dashboard qualified leads card')
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Qualified Leads</div>
                        <div class="stat-value">{{ number_format($stats['qualified_leads'] ?? 0) }}</div>
                        <a href="{{ \App\Helpers\RouteHelper::url('leads.index') }}?stage=qualified" class="stat-link">
                            View all <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('view dashboard new leads card')
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">New Leads Today</div>
                        <div class="stat-value">{{ number_format($stats['new_leads'] ?? 0) }}</div>
                        <a href="{{ \App\Helpers\RouteHelper::url('leads.index') }}?stage=new_lead" class="stat-link">
                            View all <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('view dashboard users card')
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value">{{ number_format($stats['total_users'] ?? 0) }}</div>
                        <p class="stat-description">{{ number_format($stats['total_admins'] ?? 0) }} Admins</p>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-person-gear"></i>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('view dashboard invoices card')
        <div class="col-xl-3 col-md-6">
            <div class="superadmin-stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-label">Total Invoices</div>
                        <div class="stat-value">{{ number_format($stats['total_invoices'] ?? 0) }}</div>
                        <a href="{{ \App\Helpers\RouteHelper::url('invoices.index') }}" class="stat-link">View All</a>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Charts Section -->
    @canany(['view dashboard leads chart', 'view dashboard lead stages graph'])
    @if((isset($leadsLast7Days) && count($leadsLast7Days) > 0) || (isset($leadStages) && count($leadStages) > 0))
    <div class="row g-4">
        <!-- Leads Over Time Chart -->
        @can('view dashboard leads chart')
        @if(isset($leadsLast7Days) && count($leadsLast7Days) > 0)
        <div class="col-lg-8 col-md-12">
            <div class="activity-card">
                <div class="activity-card-header">
                    <h5>
                        <i class="bi bi-graph-up"></i>
                        Leads Over Last 7 Days
                    </h5>
                </div>
                <div class="activity-card-body">
                    <div class="chart-container">
                        <canvas id="leadsChart" data-leads='@json($leadsLast7Days)'></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endcan

        <!-- Lead Stages Chart -->
        @can('view dashboard lead stages graph')
        @if(isset($leadStages) && count($leadStages) > 0)
        <div class="col-lg-4 col-md-12">
            <div class="activity-card">
                <div class="activity-card-header">
                    <h5>
                        <i class="bi bi-pie-chart"></i>
                        Lead Stages Distribution
                    </h5>
                </div>
                <div class="activity-card-body">
                    <div class="chart-container">
                        <canvas id="stagesChart" data-stages='@json($leadStages)'></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endcan
    </div>
    @endif
    @endcanany

    <!-- Follow-up Reminders and Recent Activity -->
    <div class="row g-4">
        <!-- Automated Follow-up Reminders -->
        @can('view dashboard followup reminders card')
        <div class="col-lg-6 col-md-12">
            <div class="activity-card">
                <div class="activity-card-header">
                    <h5>
                        <i class="bi bi-bell-fill"></i>
                        Automated Follow-up Reminders
                    </h5>
                    @if($followUpReminders->count() > 0)
                        <span class="badge badge-gradient-green">{{ $followUpReminders->count() }}</span>
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
                                        <span class="badge activity-badge activity-badge-gradient">
                                                <i class="bi bi-check-circle me-1"></i>{{ $daysUntilRounded }} days left
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @if($followUpReminders->count() > 8)
                                <div class="text-center pt-2">
                                <a href="{{ route('admin.leads.index') }}" class="btn btn-sm dashboard-btn-green">
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
        @endcan

        <!-- Recent Activity -->
        @can('view dashboard recent activity card')
        <div class="col-lg-6 col-md-12">
            <div class="activity-card">
                <div class="activity-card-header">
                    <h5>
                        <i class="bi bi-clock-history"></i>
                        Recent Activity
                    </h5>
                    <a href="{{ \App\Helpers\RouteHelper::url('leads.index') }}" class="btn btn-sm dashboard-btn-green">View All</a>
                </div>
                <div class="activity-card-body">
                    @if($recentActivity->count() > 0)
                            @foreach($recentActivity->take(8) as $activity)
                                @php
                                    $stageColors = [
                                        'qualified' => 'success',
                                        'new_lead' => 'primary',
                                        'in_progress' => 'info',
                                        'not_qualified' => 'warning',
                                        'junk' => 'danger'
                                    ];
                                    $stageColor = $stageColors[$activity->stage] ?? 'secondary';
                                @endphp
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="item-title flex-grow-1">
                                            <a href="{{ route('admin.leads.show', $activity) }}">
                                                <i class="bi bi-person me-2"></i>{{ $activity->name }}
                                            </a>
                                        </div>
                                    <span class="badge activity-badge activity-badge-gradient">
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
        @endcan
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Dashboard Charts JavaScript
    (function() {
        'use strict';

        // Global chart instances
        let leadsChart = null;
        let stagesChart = null;

        // Initialize all charts when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initLeadsChart();
            initStagesChart();
        });

        // Initialize Leads Over Time Chart
        function initLeadsChart() {
            const leadsCtx = document.getElementById('leadsChart');
            if (!leadsCtx) return;

            const leadsDataElement = document.getElementById('leadsChart');
            let leadsData = [];

            if (leadsDataElement && leadsDataElement.dataset.leads) {
                try {
                    leadsData = JSON.parse(leadsDataElement.dataset.leads);
                } catch (e) {
                    console.error('Error parsing leads data:', e);
                    return;
                }
            } else {
                return;
            }

            const labels = leadsData.map(item => item.date || item.day || '');
            const data = leadsData.map(item => item.count || 0);
            const maxValue = Math.max(...data, 1);

            if (typeof Chart !== 'undefined') {
                leadsChart = new Chart(leadsCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'New Leads',
                            data: data,
                            borderColor: '#84c373',
                            backgroundColor: 'rgba(132, 195, 115, 0.15)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#84c373',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: { size: 12, weight: '600' },
                                    color: '#495057'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(26, 31, 46, 0.95)',
                                padding: 12,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: Math.ceil(maxValue * 1.2),
                                ticks: {
                                    precision: 0,
                                    stepSize: 1,
                                    font: { size: 11, weight: '500' },
                                    color: '#6c757d'
                                },
                                grid: {
                                    color: 'rgba(132, 195, 115, 0.1)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    font: { size: 11, weight: '500' },
                                    color: '#6c757d'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }

        // Initialize Lead Stages Doughnut Chart
        function initStagesChart() {
            const stagesCtx = document.getElementById('stagesChart');
            if (!stagesCtx) return;

            let stagesData = {};
            const stagesDataElement = document.getElementById('stagesChart');
            if (stagesDataElement && stagesDataElement.dataset.stages) {
                try {
                    stagesData = JSON.parse(stagesDataElement.dataset.stages);
                } catch (e) {
                    console.error('Error parsing stages data:', e);
                    return;
                }
            } else {
                return;
            }

            const stageColors = {
                'new_lead': '#17a2b8',
                'in_progress': '#0dcaf0',
                'qualified': '#84c373',
                'not_qualified': '#ffc107',
                'junk': '#dc3545'
            };

            const stageLabels = Object.keys(stagesData);
            const stageValues = Object.values(stagesData);
            const stageColorsArray = stageLabels.map(stage => stageColors[stage] || '#6c757d');

            if (typeof Chart !== 'undefined') {
                stagesChart = new Chart(stagesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: stageLabels.map(label =>
                            label.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
                        ),
                        datasets: [{
                            data: stageValues,
                            backgroundColor: stageColorsArray,
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 11 },
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Update charts on window resize
        window.addEventListener('resize', function() {
            if (leadsChart) leadsChart.resize();
            if (stagesChart) stagesChart.resize();
        });
    })();
</script>
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
