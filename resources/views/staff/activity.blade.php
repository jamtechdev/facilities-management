@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid">
    <div class="staff-dashboard-header mb-3">
        <div>
            <h1 class="h3 mb-1">Activity Log</h1>
            <p class="staff-page-subtitle">View your recent clock-in and clock-out history.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($activities->count())
                <div class="list-group list-group-flush">
                    @foreach($activities as $activity)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge rounded-circle p-3 {{ $activity['type'] === 'clock_in' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                    <i class="bi {{ $activity['type'] === 'clock_in' ? 'bi-box-arrow-in-right' : 'bi-box-arrow-left' }}"></i>
                                </span>
                                <div>
                                    <h6 class="mb-1 text-capitalize">{{ str_replace('_', ' ', $activity['type']) }}</h6>
                                    <p class="mb-0 text-muted">{{ $activity['client_name'] }}</p>
                                </div>
                            </div>
                            <div class="text-muted small text-end">
                                <div>{{ $activity['timestamp']->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-activity text-muted" style="font-size:48px;"></i>
                    <p class="text-muted mt-3">No activity recorded yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

