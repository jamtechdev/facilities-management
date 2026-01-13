@extends('layouts.app')

@section('title', 'Inventory Management')

@push('styles')
    @vite(['resources/css/profile.css', 'resources/css/datatables.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Inventory Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <i class="bi bi-box-seam icon-2-5rem"></i>
            </div>
            <div class="profile-info flex-grow-1">
                <h1>Inventory Management</h1>
                <p>Manage cleaning inventory items</p>
            </div>
            @can('create inventory')
            <div class="profile-header-actions">
                <a href="{{ \App\Helpers\RouteHelper::url('inventory.create') }}" class="btn btn-light btn-lg px-4 py-2 shadow-lg rounded-pill fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i>Add Item
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Inventory Table -->
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

@push('scripts')
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
<script>
    // Pass delete route to JS for delete operations
    if (typeof window.deleteInventoryRoute === 'undefined') {
        window.deleteInventoryRoute = '{{ \App\Helpers\RouteHelper::url("inventory.destroy", ":id") }}';
    }
</script>
@endpush
@endsection
