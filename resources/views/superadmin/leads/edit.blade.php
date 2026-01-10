@extends('layouts.app')

@section('title', 'Edit Lead')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Edit Lead</h1>
                    <p class="text-muted">{{ $lead->name }}</p>
                </div>
                <a href="{{ \App\Helpers\RouteHelper::url('leads.show', $lead) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Lead
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-card">
                <div class="form-card-header">
                    <h5><i class="bi bi-pencil-square me-2"></i>Edit Lead Information</h5>
                </div>
                <div class="form-card-body">
                    <form id="updateLeadForm" method="POST" action="{{ \App\Helpers\RouteHelper::url('leads.update', $lead) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i>Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $lead->name) }}" placeholder="Enter full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $lead->email) }}" placeholder="example@email.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="company" class="form-label">Company</label>
                                <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company', $lead->company) }}">
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation', $lead->designation) }}">
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $lead->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $lead->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="source" class="form-label">
                                    <i class="bi bi-funnel me-1"></i>Source
                                </label>
                                <select class="form-select @error('source') is-invalid @enderror" id="source" name="source">
                                    <option value="">Select source</option>
                                    <option value="Website" {{ old('source', $lead->source) == 'Website' ? 'selected' : '' }}>Website</option>
                                    <option value="Referral" {{ old('source', $lead->source) == 'Referral' ? 'selected' : '' }}>Referral</option>
                                    <option value="Cold Call" {{ old('source', $lead->source) == 'Cold Call' ? 'selected' : '' }}>Cold Call</option>
                                    <option value="LinkedIn" {{ old('source', $lead->source) == 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                                    <option value="Other" {{ old('source', $lead->source) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="stage" class="form-label">Stage <span class="text-danger">*</span></label>
                                <select class="form-select @error('stage') is-invalid @enderror" id="stage" name="stage" required>
                                    <option value="new_lead" {{ old('stage', $lead->stage) == 'new_lead' ? 'selected' : '' }}>New Lead</option>
                                    <option value="in_progress" {{ old('stage', $lead->stage) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="qualified" {{ old('stage', $lead->stage) == 'qualified' ? 'selected' : '' }}>Qualified</option>
                                    <option value="not_qualified" {{ old('stage', $lead->stage) == 'not_qualified' ? 'selected' : '' }}>Not Qualified</option>
                                    <option value="junk" {{ old('stage', $lead->stage) == 'junk' ? 'selected' : '' }}>Junk</option>
                                </select>
                                @error('stage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_staff_id" class="form-label">Assigned Staff</label>
                                <select class="form-select @error('assigned_staff_id') is-invalid @enderror" id="assigned_staff_id" name="assigned_staff_id">
                                    <option value="">Select Staff</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->id }}" {{ old('assigned_staff_id', $lead->assigned_staff_id) == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_staff_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Update Lead
                            </button>
                            <a href="{{ \App\Helpers\RouteHelper::url('leads.show', $lead) }}" class="btn btn-outline-secondary">
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
