@extends('layouts.app')

@section('title', 'Staff Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Staff Details</h1>
                    <p class="text-muted">{{ $staff->name }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-primary me-2">
                        <i class="bi bi-pencil me-2"></i>Edit Staff
                    </a>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Staff
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Staff Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Staff Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Name</label>
                            <p class="mb-0"><strong>{{ $staff->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-0">{{ $staff->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Mobile</label>
                            <p class="mb-0">{{ $staff->mobile ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Hourly Rate</label>
                            <p class="mb-0">£{{ number_format($staff->hourly_rate ?? 0, 2) }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Address</label>
                            <p class="mb-0">{{ $staff->address ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Weekly Hours</label>
                            <p class="mb-0">{{ $staff->assigned_weekly_hours ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Monthly Hours</label>
                            <p class="mb-0">{{ $staff->assigned_monthly_hours ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Status</label>
                            <p class="mb-0">
                                @if($staff->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Clients -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Assigned Clients</h5>
                </div>
                <div class="card-body">
                    @if($staff->clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Weekly Hours</th>
                                        <th>Monthly Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staff->clients as $client)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.clients.show', $client) }}" class="text-decoration-none">
                                                    {{ $client->company_name }}
                                                </a>
                                            </td>
                                            <td>{{ $client->pivot->assigned_weekly_hours ?? '-' }}</td>
                                            <td>{{ $client->pivot->assigned_monthly_hours ?? '-' }}</td>
                                            <td>
                                                @if($client->pivot->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No clients assigned yet</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Assigned Clients</small>
                        <h4>{{ $staff->clients->where('pivot.is_active', true)->count() }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Total Timesheets</small>
                        <h4>{{ $staff->timesheets->count() }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Assigned Leads</small>
                        <h4>{{ $staff->leads->count() }}</h4>
                    </div>
                    <div>
                        <small class="text-muted">Created</small>
                        <p class="mb-0">{{ $staff->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

