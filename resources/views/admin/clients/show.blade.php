@extends('layouts.app')

@section('title', 'Client Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Client Details</h1>
                    <p class="text-muted">{{ $client->company_name }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-primary me-2">
                        <i class="bi bi-pencil me-2"></i>Edit Client
                    </a>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Clients
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Client Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Client Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Company Name</label>
                            <p class="mb-0"><strong>{{ $client->company_name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Contact Person</label>
                            <p class="mb-0">{{ $client->contact_person }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-0">{{ $client->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Phone</label>
                            <p class="mb-0">{{ $client->phone ?? '-' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Address</label>
                            <p class="mb-0">{{ $client->address ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">City</label>
                            <p class="mb-0">{{ $client->city ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Postal Code</label>
                            <p class="mb-0">{{ $client->postal_code ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Status</label>
                            <p class="mb-0">
                                @if($client->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Billing Frequency</label>
                            <p class="mb-0">
                                @if($client->billing_frequency)
                                    <span class="badge bg-info">{{ ucfirst($client->billing_frequency) }}</span>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Weekly Hours</label>
                            <p class="mb-0">{{ $client->agreed_weekly_hours ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted">Monthly Hours</label>
                            <p class="mb-0">{{ $client->agreed_monthly_hours ?? '-' }}</p>
                        </div>
                        @if($client->lead)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Converted From Lead</label>
                            <p class="mb-0">
                                <a href="{{ route('admin.leads.show', $client->lead) }}" class="text-decoration-none">
                                    {{ $client->lead->name }}
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </p>
                        </div>
                        @endif
                        @if($client->notes)
                        <div class="col-12">
                            <label class="form-label text-muted">Notes</label>
                            <p class="mb-0">{{ $client->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assigned Staff -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Assigned Staff</h5>
                </div>
                <div class="card-body">
                    @if($client->staff->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Weekly Hours</th>
                                        <th>Monthly Hours</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($client->staff as $staff)
                                        <tr>
                                            <td>{{ $staff->name }}</td>
                                            <td>{{ $staff->pivot->assigned_weekly_hours ?? '-' }}</td>
                                            <td>{{ $staff->pivot->assigned_monthly_hours ?? '-' }}</td>
                                            <td>
                                                @if($staff->pivot->is_active)
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
                        <p class="text-muted text-center py-3">No staff assigned yet</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="mailto:{{ $client->email }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-envelope me-2"></i>Send Email
                    </a>
                    <a href="tel:{{ $client->phone }}" class="btn btn-outline-success w-100 mb-2">
                        <i class="bi bi-telephone me-2"></i>Call
                    </a>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Total Timesheets</small>
                        <h4>{{ $client->timesheets->count() }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Total Invoices</small>
                        <h4>{{ $client->invoices->count() }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Job Photos</small>
                        <h4>{{ $client->jobPhotos->count() }}</h4>
                    </div>
                    <div>
                        <small class="text-muted">Created</small>
                        <p class="mb-0">{{ $client->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

