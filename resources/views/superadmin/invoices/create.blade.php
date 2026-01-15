@extends('layouts.app')

@section('title', 'Create Invoice')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Invoice Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    <i class="bi bi-receipt icon-2-5rem"></i>
                </div>
                <div class="profile-info flex-grow-1">
                    <h1>Create Invoice</h1>
                    <p>Generate a new invoice for a client</p>
                </div>
                <div class="profile-header-actions">
                    <a href="{{ \App\Helpers\RouteHelper::url('invoices.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left me-2"></i>Back to Invoices
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-card">
                    <div class="form-card-header">
                        <h5><i class="bi bi-receipt me-2"></i>Invoice Information</h5>
                    </div>
                    <div class="form-card-body">
                        <form id="invoiceForm" method="POST"
                            action="{{ \App\Helpers\RouteHelper::url('invoices.store') }}">
                            @csrf

                            <!-- Show all validation errors in one place -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="client_id" class="form-label">
                                        <i class="bi bi-building me-1"></i>Company <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="client_id" name="client_id" required>
                                        <option value="">Select a company...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="hourly_rate" class="form-label">
                                        <i class="bi bi-currency-pound me-1"></i>Hourly Rate <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="hourly_rate"
                                        name="hourly_rate" value="{{ old('hourly_rate') }}" placeholder="0.00" required>
                                    @error('hourly_rate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="billing_period_start" class="form-label">
                                        <i class="bi bi-calendar-event me-1"></i>Billing Period Start <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="billing_period_start"
                                        name="billing_period_start" value="{{ old('billing_period_start') }}" required>
                                    @error('billing_period_start')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="billing_period_end" class="form-label">
                                        <i class="bi bi-calendar-check me-1"></i>Billing Period End <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="billing_period_end"
                                        name="billing_period_end" value="{{ old('billing_period_end') }}" required>
                                    @error('billing_period_end')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="tax_rate" class="form-label">
                                        <i class="bi bi-percent me-1"></i>Tax Rate (%)
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate"
                                        value="{{ old('tax_rate', 0) }}" placeholder="0.00">
                                    @error('tax_rate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">
                                        <i class="bi bi-sticky me-1"></i>Notes
                                    </label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="Additional notes about this invoice...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-check-circle me-2"></i>Create Invoice
                                </button>
                                <a href="{{ \App\Helpers\RouteHelper::url('invoices.index') }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
