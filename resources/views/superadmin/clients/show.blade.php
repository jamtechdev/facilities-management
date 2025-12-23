@extends('layouts.app')

@section('title', 'Client Details')

@push('styles')
    @vite(['resources/css/entity-details.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Client Header -->
    <x-header-card 
        :title="$client->company_name"
        :contactPerson="$client->contact_person"
        :email="$client->email"
        :phone="$client->phone"
        type="client">
        <x-slot name="actions">
            <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-light me-2">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </x-slot>
    </x-header-card>

    <!-- Tabs Navigation -->
    <x-tab-navigation 
        :tabs="[
            ['id' => 'info', 'label' => 'Information', 'icon' => 'bi-info-circle'],
            ['id' => 'staff', 'label' => 'Assigned Staff', 'icon' => 'bi-people', 'badge' => $client->staff->count()],
            ['id' => 'service', 'label' => 'Service History', 'icon' => 'bi-clock-history', 'badge' => $client->timesheets->count()],
            ['id' => 'communications', 'label' => 'Communications', 'icon' => 'bi-chat-dots', 'badge' => $client->communications->count()],
            ['id' => 'documents', 'label' => 'Documents', 'icon' => 'bi-file-earmark', 'badge' => $client->documents->count()],
            ['id' => 'feedback', 'label' => 'Feedback', 'icon' => 'bi-star', 'badge' => $client->feedback->count()],
            ['id' => 'invoices', 'label' => 'Invoices', 'icon' => 'bi-receipt', 'badge' => $client->invoices->count()]
        ]"
        id="clientTabs" />

    <!-- Tab Content -->
    <div class="tab-content" id="clientTabsContent">
        <!-- Information Tab -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="row g-3">
                <x-info-card label="Company Name" :value="$client->company_name" />
                <x-info-card label="Contact Person" :value="$client->contact_person" />
                <x-info-card label="Email" :value="$client->email" :link="'mailto:' . $client->email" />
                <x-info-card label="Phone" :value="$client->phone ?? '-'" :link="$client->phone ? 'tel:' . $client->phone : null" />
                <x-info-card label="Address" :value="$client->address ?? '-'" />
                <x-info-card label="City" :value="$client->city ?? '-'" />
                <x-info-card label="Postal Code" :value="$client->postal_code ?? '-'" />
                <x-info-card 
                    label="Status" 
                    :badge="$client->is_active ? 'Active' : 'Inactive'" 
                    :badgeColor="$client->is_active ? 'success' : 'secondary'" />
                <x-info-card 
                    label="Billing Frequency" 
                    :badge="$client->billing_frequency ? ucfirst($client->billing_frequency) : 'Not set'" 
                    badgeColor="info" />
                <x-info-card label="Agreed Weekly Hours" :value="$client->agreed_weekly_hours ? $client->agreed_weekly_hours . ' hours' : '-'" />
                <x-info-card label="Agreed Monthly Hours" :value="$client->agreed_monthly_hours ? $client->agreed_monthly_hours . ' hours' : '-'" />
                @if($client->lead)
                    <x-info-card 
                        label="Converted From Lead" 
                        :value="$client->lead->name" 
                        :link="route('admin.leads.show', $client->lead)" />
                @endif
                @if($client->notes)
                    <div class="col-12">
                        <x-info-card label="Notes" :value="$client->notes" />
                    </div>
                @endif
                <x-info-card label="Created" :value="$client->created_at->format('M d, Y h:i A')" />
                <x-info-card label="Last Updated" :value="$client->updated_at->format('M d, Y h:i A')" />
            </div>
        </div>

        <!-- Assigned Staff Tab -->
        <div class="tab-pane fade" id="staff" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Assigned Staff</h5>
            </div>
            
            @if($client->staff->count() > 0)
                @foreach($client->staff as $staff)
                    <div class="staff-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2">
                                    <a href="{{ route('admin.staff.show', $staff) }}" class="text-decoration-none">
                                        <i class="bi bi-person me-2"></i>{{ $staff->name }}
                                    </a>
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Weekly Hours</small>
                                        <strong>{{ $staff->pivot->assigned_weekly_hours ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Monthly Hours</small>
                                        <strong>{{ $staff->pivot->assigned_monthly_hours ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Status</small>
                                        @if($staff->pivot->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 48px; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No staff assigned yet</p>
                </div>
            @endif
        </div>

        <!-- Service History Tab -->
        <div class="tab-pane fade" id="service" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Service History</h5>
            </div>
            
            @if($client->timesheets->count() > 0)
                @foreach($client->timesheets->sortByDesc('work_date') as $timesheet)
                    <div class="service-history-item">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">
                                    <i class="bi bi-calendar-event me-2"></i>{{ $timesheet->work_date->format('M d, Y') }}
                                </h6>
                                <p class="mb-1 text-muted">
                                    <i class="bi bi-person me-2"></i>{{ $timesheet->staff->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="mb-1">
                                    <strong class="text-primary">{{ number_format($timesheet->hours_worked, 2) }} hours</strong>
                                </div>
                                @if($timesheet->is_approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($timesheet->clock_in_time && $timesheet->clock_out_time)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($timesheet->clock_in_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($timesheet->clock_out_time)->format('h:i A') }}
                                </small>
                            </div>
                        @endif
                        
                        @if($timesheet->notes)
                            <div class="mb-2">
                                <small class="text-muted">{{ $timesheet->notes }}</small>
                            </div>
                        @endif
                        
                        @if($timesheet->jobPhotos->count() > 0)
                            <div class="photo-gallery">
                                @foreach($timesheet->jobPhotos as $photo)
                                    <div class="photo-item">
                                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Job Photo" onclick="openImageModal('{{ asset('storage/' . $photo->photo_path) }}', '{{ $photo->photo_type }}')">
                                        <span class="photo-badge bg-{{ $photo->photo_type == 'before' ? 'warning' : 'success' }}">
                                            {{ ucfirst($photo->photo_type) }}
                                        </span>
                                        @if($photo->is_approved)
                                            <span class="badge bg-success" style="position: absolute; bottom: 8px; left: 8px; font-size: 10px;">
                                                <i class="bi bi-check-circle"></i>
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clock-history" style="font-size: 48px; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No service history yet</p>
                </div>
            @endif
        </div>

        <!-- Communications Tab -->
        <div class="tab-pane fade" id="communications" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Communication History</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommunicationModal">
                    <i class="bi bi-plus-circle me-2"></i>Add Communication
                </button>
            </div>
            
            @if($client->communications->count() > 0)
                @foreach($client->communications->sortByDesc('created_at') as $communication)
                    <div class="communication-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-{{ $communication->type == 'email' ? 'primary' : ($communication->type == 'call' ? 'success' : 'info') }} me-2">
                                    {{ ucfirst($communication->type) }}
                                </span>
                                @if($communication->subject)
                                    <strong>{{ $communication->subject }}</strong>
                                @endif
                            </div>
                            <small class="text-muted">{{ $communication->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        <p class="mb-1">{{ $communication->message }}</p>
                        <small class="text-muted">
                            @if($communication->user)
                                by {{ $communication->user->name }}
                            @endif
                        </small>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots" style="font-size: 48px; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No communications yet</p>
                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addCommunicationModal">
                        <i class="bi bi-plus-circle me-2"></i>Add First Communication
                    </button>
                </div>
            @endif
        </div>

        <!-- Documents Tab -->
        <div class="tab-pane fade" id="documents" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Documents</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                    <i class="bi bi-upload me-2"></i>Upload Document
                </button>
            </div>
            
            @if($client->documents->count() > 0)
                @foreach($client->documents as $document)
                    <div class="document-item">
                        <div>
                            <i class="bi bi-file-earmark me-2"></i>
                            <strong>{{ $document->name }}</strong>
                            <span class="badge bg-secondary ms-2">{{ ucfirst($document->document_type) }}</span>
                            <small class="text-muted ms-2">{{ $document->created_at->format('M d, Y') }}</small>
                        </div>
                        <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark" style="font-size: 48px; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No documents uploaded yet</p>
                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                        <i class="bi bi-upload me-2"></i>Upload First Document
                    </button>
                </div>
            @endif
        </div>

        <!-- Feedback Tab -->
        <div class="tab-pane fade" id="feedback" role="tabpanel">
            <h5 class="mb-4">Customer Feedback</h5>
            
            @if($client->feedback->count() > 0)
                @foreach($client->feedback->sortByDesc('created_at') as $fb)
                    <div class="communication-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>Feedback</strong>
                                <span class="badge bg-{{ $fb->rating >= 4 ? 'success' : ($fb->rating >= 3 ? 'warning' : 'danger') }} ms-2">
                                    {{ $fb->rating }}/5
                                </span>
                            </div>
                            <small class="text-muted">{{ $fb->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        @if($fb->message)
                            <p class="mb-1">{{ $fb->message }}</p>
                        @endif
                        @if($fb->name)
                            <small class="text-muted">From: {{ $fb->name }}</small>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-star" style="font-size: 48px; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No feedback received yet</p>
                </div>
            @endif
        </div>

        <!-- Invoices Tab -->
        <div class="tab-pane fade" id="invoices" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Invoices</h5>
                <a href="{{ route('admin.invoices.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create Invoice
                </a>
            </div>
            
            @if($client->invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Billing Period</th>
                                <th>Hours</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->invoices->sortByDesc('created_at') as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-decoration-none">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->billing_period }}</td>
                                    <td>{{ number_format($invoice->hours_worked, 2) }}</td>
                                    <td>£{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.invoices.download', $invoice) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-receipt" style="font-size: 48px; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No invoices yet</p>
                    <a href="{{ route('admin.invoices.create', ['client_id' => $client->id]) }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Create First Invoice
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Communication Modal -->
@include('superadmin.communications.modal', ['communicable' => $client])

<!-- Upload Document Modal -->
@include('superadmin.documents.modal', ['documentable' => $client])

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Job Photo - <span id="photoType"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Job Photo" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @vite(['resources/js/entity-details.js'])
@endpush
