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
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        <i class="bi bi-person-lines-fill icon-2-5rem"></i>
                    @endif
                </div>
                <div class="profile-info flex-grow-1">
                    <h1>Invoices</h1>
                    <p>Manage client invoices</p>
                </div>
                @can('create invoices')
                    <div class="profile-header-actions">
                        <a href="{{ \App\Helpers\RouteHelper::url('invoices.create') }}"
                            class="btn btn-light btn-lg px-4 py-2 shadow-lg rounded-pill fw-semibold">
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
        <script>
            // Pass delete route to JS for delete operations
            if (typeof window.deleteInvoiceRoute === 'undefined') {
                window.deleteInvoiceRoute = '{{ \App\Helpers\RouteHelper::url('invoices.destroy', ':id') }}';
            }
        </script>
    @endpush
@endsection
