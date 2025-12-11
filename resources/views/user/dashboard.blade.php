@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">User Dashboard</h1>
            <p class="text-muted">Welcome to your dashboard</p>
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

