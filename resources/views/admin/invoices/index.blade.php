@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Invoices</h1>
                    <p class="text-muted">Manage client invoices</p>
                </div>
                <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>New Invoice
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-striped table-hover table-responsive', 'style' => 'width:100%']) !!}
        </div>
    </div>
</div>

@push('scripts')
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
@endpush
@endsection

