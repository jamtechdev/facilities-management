@extends('layouts.app')

@section('title', 'Invoices')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Invoices Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <i class="bi bi-receipt icon-2-5rem"></i>
            </div>
            <div class="profile-info flex-grow-1">
                <h1>Invoices</h1>
                <p>Manage client invoices</p>
            </div>
            @can('create invoices')
            <div class="profile-header-actions">
                <a href="{{ \App\Helpers\RouteHelper::url('invoices.create') }}" class="btn btn-light btn-lg px-4 py-2 shadow-lg rounded-pill fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i>New Invoice
                </a>
            </div>
            @endcan
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-striped table-hover w-100']) !!}
            </div>
        </div>
    </div>
</div>

@push('scripts')
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
@endpush
@endsection

