@extends('layouts.app')

@section('title', 'Clients Management')


@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Clients Management</h1>
                    <p class="text-muted">Manage all your clients and their information</p>
                </div>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Client
                </a>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="row">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    {!! $dataTable->table(['class' => 'table table-striped table-hover table-responsive', 'style' => 'width:100%']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
@endpush

