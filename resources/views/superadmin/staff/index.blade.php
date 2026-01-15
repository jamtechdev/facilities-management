@extends('layouts.app')

@section('title', 'Staff Management')


@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Staff Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        <i class="bi bi-person-lines-fill icon-2-5rem"></i>
                    @endif
                </div>
                <div class="profile-info flex-grow-1">
                    <h1>Staff Management</h1>
                    <p>Manage all your staff members</p>
                </div>
                @can('create staff')
                    <div class="profile-header-actions">
                        <a href="{{ \App\Helpers\RouteHelper::url('staff.create') }}"
                            class="btn btn-light btn-lg px-4 py-2 shadow-lg rounded-pill fw-semibold">
                            <i class="bi bi-plus-circle me-2"></i>Create New Staff
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Staff Table -->
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
    <script>
        // Pass delete route to JS for delete operations
        if (typeof window.deleteStaffRoute === 'undefined') {
            window.deleteStaffRoute = '{{ \App\Helpers\RouteHelper::url('staff.destroy', ':id') }}';
        }
    </script>
@endpush
