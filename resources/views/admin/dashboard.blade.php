@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    @vite(['resources/css/dashboard.css'])
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Admin Dashboard</h1>
            <p class="text-muted">Welcome to the admin dashboard</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <x-stats-card 
                icon="bi-person-lines-fill"
                iconColor="primary"
                label="Total Leads"
                :value="number_format($stats['total_leads'])"
            />
        </div>

        <div class="col-md-3">
            <x-stats-card 
                icon="bi-building"
                iconColor="primary"
                label="Active Clients"
                :value="number_format($stats['total_clients'])"
            />
        </div>

        <div class="col-md-3">
            <x-stats-card 
                icon="bi-people"
                iconColor="primary"
                label="Active Staff"
                :value="number_format($stats['total_staff'])"
            />
        </div>

        <div class="col-md-3">
            <x-stats-card 
                icon="bi-receipt"
                iconColor="primary"
                label="Total Invoices"
                :value="number_format($stats['total_invoices'])"
            />
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-3">
            <x-stats-card 
                icon="bi-star-fill"
                iconColor="primary"
                label="Qualified Leads"
                :value="number_format($stats['qualified_leads'])"
            />
        </div>

        <div class="col-md-3">
            <x-stats-card 
                icon="bi-plus-circle"
                iconColor="primary"
                label="New Leads Today"
                :value="number_format($stats['new_leads'])"
            />
        </div>

        <div class="col-md-3">
            <x-stats-card 
                icon="bi-currency-pound"
                iconColor="primary"
                label="Revenue"
                :value="'£' . number_format($stats['revenue'], 2)"
            />
        </div>

        <div class="col-md-3">
            <x-stats-card 
                icon="bi-shield-check"
                iconColor="danger"
                label="Role"
                :badge="['text' => 'Admin', 'color' => 'danger']"
            />
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Admin Information</h5>
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
                                        <span class="badge bg-danger">{{ $role->name }}</span>
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

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Admin Features</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">As an administrator, you have access to all system features and can manage users, roles, and permissions.</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        This is a protected admin area. Only users with the Admin role can access this dashboard.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-primary">
                            <i class="bi bi-person-lines-fill me-2"></i>Manage Leads
                        </a>
                        <a href="{{ route('admin.leads.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create New Lead
                        </a>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-primary">
                            <i class="bi bi-building me-2"></i>Manage Clients
                        </a>
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-primary">
                            <i class="bi bi-people me-2"></i>Manage Staff
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                            <i class="bi bi-person-gear me-2"></i>Manage Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Spatie Permissions Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Your Roles:</h6>
                            <div class="mb-3">
                                @foreach(auth()->user()->roles as $role)
                                    <span class="badge bg-danger me-2">{{ $role->name }}</span>
                                @endforeach
                            </div>
                            <h6>Role Check Methods:</h6>
                            <ul class="list-unstyled">
                                <li><code>hasRole('Admin')</code>: 
                                    <span class="badge bg-{{ auth()->user()->hasRole('Admin') ? 'success' : 'danger' }}">
                                        {{ auth()->user()->hasRole('Admin') ? 'true' : 'false' }}
                                    </span>
                                </li>
                                <li><code>hasRole('User')</code>: 
                                    <span class="badge bg-{{ auth()->user()->hasRole('User') ? 'success' : 'danger' }}">
                                        {{ auth()->user()->hasRole('User') ? 'true' : 'false' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>All System Roles:</h6>
                            @php
                                $allRoles = \Spatie\Permission\Models\Role::all();
                            @endphp
                            <div class="mb-3">
                                @foreach($allRoles as $role)
                                    <span class="badge bg-{{ $role->name === 'Admin' ? 'danger' : 'primary' }} me-2">
                                        {{ $role->name }} 
                                        <small>({{ $role->users->count() }} users)</small>
                                    </span>
                                @endforeach
                            </div>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Spatie Laravel Permission</strong> is properly configured and working!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

