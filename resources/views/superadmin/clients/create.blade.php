@extends('layouts.app')

@section('title', 'Create New Client')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Create New Client</h1>
                    <p class="text-muted">Add a new client to your system</p>
                </div>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Clients
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-card">
                <div class="form-card-header">
                    <h5><i class="bi bi-building-add me-2"></i>Client Information</h5>
                </div>
                <div class="form-card-body">
                    <form id="createClientForm" method="POST" action="{{ route('admin.clients.store') }}">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">
                                    <i class="bi bi-building me-1"></i>Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Enter company name" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">
                                    <i class="bi bi-person me-1"></i>Contact Person <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" placeholder="Contact person name" required>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="billing_frequency" class="form-label">Billing Frequency</label>
                                <select class="form-select @error('billing_frequency') is-invalid @enderror" id="billing_frequency" name="billing_frequency">
                                    <option value="">Select Frequency</option>
                                    <option value="weekly" {{ old('billing_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="bi-weekly" {{ old('billing_frequency') == 'bi-weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                                    <option value="monthly" {{ old('billing_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('billing_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                </select>
                                @error('billing_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="agreed_weekly_hours" class="form-label">Agreed Weekly Hours</label>
                                <input type="number" step="0.5" class="form-control @error('agreed_weekly_hours') is-invalid @enderror" id="agreed_weekly_hours" name="agreed_weekly_hours" value="{{ old('agreed_weekly_hours') }}">
                                @error('agreed_weekly_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="agreed_monthly_hours" class="form-label">Agreed Monthly Hours</label>
                                <input type="number" step="0.5" class="form-control @error('agreed_monthly_hours') is-invalid @enderror" id="agreed_monthly_hours" name="agreed_monthly_hours" value="{{ old('agreed_monthly_hours') }}">
                                @error('agreed_monthly_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Client
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Create Client
                            </button>
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
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

@push('scripts')
    @vite(['resources/js/forms.js'])
@endpush

