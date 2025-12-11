@extends('layouts.app')

@section('title', 'Lead Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Lead Details</h1>
                    <p class="text-muted">{{ $lead->name }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-primary me-2">
                        <i class="bi bi-pencil me-2"></i>Edit Lead
                    </a>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Leads
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Lead Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Lead Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Name</label>
                            <p class="mb-0"><strong>{{ $lead->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-0">{{ $lead->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Company</label>
                            <p class="mb-0">{{ $lead->company ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Designation</label>
                            <p class="mb-0">{{ $lead->designation ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Phone</label>
                            <p class="mb-0">{{ $lead->phone ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">City</label>
                            <p class="mb-0">{{ $lead->city ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Source</label>
                            <p class="mb-0">{{ $lead->source ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Stage</label>
                            <p class="mb-0">
                                @php
                                    $stageColors = [
                                        'new_lead' => 'primary',
                                        'in_progress' => 'info',
                                        'qualified' => 'success',
                                        'not_qualified' => 'warning',
                                        'junk' => 'danger'
                                    ];
                                    $stageLabels = [
                                        'new_lead' => 'New Lead',
                                        'in_progress' => 'In Progress',
                                        'qualified' => 'Qualified',
                                        'not_qualified' => 'Not Qualified',
                                        'junk' => 'Junk'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $stageColors[$lead->stage] ?? 'secondary' }} fs-6">
                                    {{ $stageLabels[$lead->stage] ?? $lead->stage }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Assigned Staff</label>
                            <p class="mb-0">
                                @if($lead->assignedStaff)
                                    {{ $lead->assignedStaff->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </p>
                        </div>
                        @if($lead->convertedToClient)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Converted To Client</label>
                            <p class="mb-0">
                                <a href="{{ route('admin.clients.show', $lead->convertedToClient) }}" class="text-decoration-none">
                                    {{ $lead->convertedToClient->company_name }}
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </p>
                        </div>
                        @endif
                        @if($lead->notes)
                        <div class="col-12">
                            <label class="form-label text-muted">Notes</label>
                            <p class="mb-0">{{ $lead->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Communications -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Communications</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCommunicationModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Communication
                    </button>
                </div>
                <div class="card-body">
                    @if($lead->communications->count() > 0)
                        <div class="list-group">
                            @foreach($lead->communications as $communication)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge bg-{{ $communication->type == 'email' ? 'primary' : ($communication->type == 'call' ? 'success' : 'info') }}">
                                                    {{ ucfirst($communication->type) }}
                                                </span>
                                                @if($communication->subject)
                                                    {{ $communication->subject }}
                                                @endif
                                            </h6>
                                            <p class="mb-1">{{ $communication->message }}</p>
                                            <small class="text-muted">
                                                {{ $communication->created_at->format('M d, Y h:i A') }}
                                                @if($communication->user)
                                                    by {{ $communication->user->name }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No communications yet</p>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Documents</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                        <i class="bi bi-upload me-1"></i>Upload Document
                    </button>
                </div>
                <div class="card-body">
                    @if($lead->documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lead->documents as $document)
                                        <tr>
                                            <td>{{ $document->name }}</td>
                                            <td><span class="badge bg-secondary">{{ ucfirst($document->document_type) }}</span></td>
                                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No documents uploaded yet</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    @if(!$lead->converted_to_client_id && $lead->stage == 'qualified')
                        <button type="button" class="btn btn-success w-100 mb-2" id="convertToClientBtn">
                            <i class="bi bi-check-circle me-2"></i>Convert to Client
                        </button>
                    @endif
                    <a href="mailto:{{ $lead->email }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-envelope me-2"></i>Send Email
                    </a>
                    <a href="tel:{{ $lead->phone }}" class="btn btn-outline-success w-100 mb-2">
                        <i class="bi bi-telephone me-2"></i>Call
                    </a>
                </div>
            </div>

            <!-- Follow-up Tasks -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Follow-up Tasks</h5>
                </div>
                <div class="card-body">
                    @if($lead->followUpTasks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($lead->followUpTasks as $task)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">Day {{ $task->reminder_day }} Reminder</h6>
                                            <p class="mb-1 small">{{ $task->suggestion }}</p>
                                            <small class="text-muted">Due: {{ $task->due_date->format('M d, Y') }}</small>
                                        </div>
                                        @if($task->is_completed)
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small">No follow-up tasks</p>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <small class="text-muted">Created</small>
                            <p class="mb-0">{{ $lead->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($lead->converted_at)
                        <div class="timeline-item">
                            <small class="text-muted">Converted to Client</small>
                            <p class="mb-0">{{ $lead->converted_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        <div class="timeline-item">
                            <small class="text-muted">Last Updated</small>
                            <p class="mb-0">{{ $lead->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const convertBtn = document.getElementById('convertToClientBtn');
        
        if (convertBtn) {
            convertBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to convert this lead to a client?')) {
                    const leadId = {{ $lead->id }};
                    const btnText = convertBtn.innerHTML;
                    convertBtn.disabled = true;
                    convertBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Converting...';
                    
                    axios.post(`/admin/leads/${leadId}/convert`, {}, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(function(response) {
                        if (response.data.success) {
                            window.location.href = response.data.redirect;
                        }
                    })
                    .catch(function(error) {
                        convertBtn.disabled = false;
                        convertBtn.innerHTML = btnText;
                        alert(error.response?.data?.message || 'Failed to convert lead');
                    });
                }
            });
        }
    });
</script>
@endpush

