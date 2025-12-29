@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Edit Staff</h1>
                    <p class="text-muted">{{ $staff->name }}</p>
                </div>
                <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Staff
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="form-card">
                <div class="form-card-header">
                    <h5><i class="bi bi-pencil-square me-2"></i>Edit Staff Information</h5>
                </div>
                <div class="form-card-body">
                    <form id="updateStaffForm" method="POST" action="{{ route('admin.staff.update', $staff) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $staff->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $staff->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile', $staff->mobile) }}">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hourly_rate" class="form-label">Hourly Rate (£)</label>
                                <input type="number" step="0.01" class="form-control @error('hourly_rate') is-invalid @enderror" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', $staff->hourly_rate) }}">
                                @error('hourly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $staff->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_weekly_hours" class="form-label">Assigned Weekly Hours</label>
                                <input type="number" step="0.5" class="form-control @error('assigned_weekly_hours') is-invalid @enderror" id="assigned_weekly_hours" name="assigned_weekly_hours" value="{{ old('assigned_weekly_hours', $staff->assigned_weekly_hours) }}">
                                @error('assigned_weekly_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_monthly_hours" class="form-label">Assigned Monthly Hours</label>
                                <input type="number" step="0.5" class="form-control @error('assigned_monthly_hours') is-invalid @enderror" id="assigned_monthly_hours" name="assigned_monthly_hours" value="{{ old('assigned_monthly_hours', $staff->assigned_monthly_hours) }}">
                                @error('assigned_monthly_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $staff->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Staff
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Update Staff
                            </button>
                            <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline-secondary">
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
