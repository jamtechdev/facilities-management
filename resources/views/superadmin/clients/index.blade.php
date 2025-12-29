@extends('layouts.app')

@section('title', 'Clients Management')


@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Clients Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <i class="bi bi-building icon-2-5rem"></i>
            </div>
            <div class="profile-info flex-grow-1">
                <h1>Clients Management</h1>
                <p>Manage all your clients and their information</p>
            </div>
            @can('create clients')
            <div class="profile-header-actions">
                <a href="{{ route('admin.clients.create') }}" class="btn btn-light btn-lg px-4 py-2 shadow-lg rounded-pill fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i>Create New Client
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Clients Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-striped table-hover w-100']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
@endpush

