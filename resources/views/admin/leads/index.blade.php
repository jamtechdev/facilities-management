@extends('layouts.app')

@section('title', 'Leads Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Leads Management</h1>
                    <p class="text-muted">Manage all your leads and track their progress</p>
                </div>
                <a href="{{ route('admin.leads.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Lead
                </a>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
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

