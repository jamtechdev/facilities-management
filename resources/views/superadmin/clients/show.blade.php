@extends('layouts.app')

@section('title', 'Client Details')

@push('styles')
    @vite(['resources/css/entity-details.css', 'resources/css/common-styles.css'])
@endpush

@push('scripts')
    @vite(['resources/js/image-modal.js'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Client Header -->
        <x-header-card :title="$client->company_name" :contactPerson="$client->contact_person" :email="$client->email" :phone="$client->phone" type="client">
            <x-slot name="actions">
                @if (auth()->user()->can('view admin dashboard'))
                    <button class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#sendEmailModal">
                        <i class="bi bi-envelope me-2"></i>Send Email
                    </button>
                @endif
                <a href="{{ \App\Helpers\RouteHelper::url('clients.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </x-slot>
        </x-header-card>

        <!-- Tabs Navigation -->
        <x-tab-navigation :tabs="[
            ['id' => 'info', 'label' => 'Information', 'icon' => 'bi-info-circle'],
            ['id' => 'staff', 'label' => 'Assigned Staff', 'icon' => 'bi-people', 'badge' => $client->staff->count()],
            [
                'id' => 'service',
                'label' => 'Service History',
                'icon' => 'bi-clock-history',
                'badge' => $client->timesheets->count(),
            ],
            [
                'id' => 'communications',
                'label' => 'Communications',
                'icon' => 'bi-chat-dots',
                'badge' => $client->communications->count(),
            ],
            [
                'id' => 'documents',
                'label' => 'Documents',
                'icon' => 'bi-file-earmark',
                'badge' => $client->documents->count(),
            ],
            ['id' => 'feedback', 'label' => 'Feedback', 'icon' => 'bi-star', 'badge' => $client->feedback->count()],
            ['id' => 'invoices', 'label' => 'Invoices', 'icon' => 'bi-receipt', 'badge' => $client->invoices->count()],
        ]" id="clientTabs" />

        <!-- Tab Content -->
        <div class="tab-content" id="clientTabsContent">
            <!-- Information Tab -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row g-3">
                    <!-- Company Information Section -->
                    <div class="col-12">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-building me-2"></i>Company Information
                        </h5>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Company Name" :value="$client->company_name" field="company_name"
                            entityType="clients" :entityId="$client->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Contact Person" :value="$client->contact_person" field="contact_person"
                            entityType="clients" :entityId="$client->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Email" :value="$client->email" :link="'mailto:' . $client->email" field="email"
                            entityType="clients" :entityId="$client->id" fieldType="email" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Phone" :value="$client->phone ?? '-'" :link="$client->phone ? 'tel:' . $client->phone : null" field="phone"
                            entityType="clients" :entityId="$client->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="City" :value="$client->city ?? '-'" field="city" entityType="clients"
                            :entityId="$client->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Postal Code" :value="$client->postal_code ?? '-'" field="postal_code"
                            entityType="clients" :entityId="$client->id" fieldType="text" />
                    </div>
                    <div class="col-12">
                        <x-editable-info-card label="Address" :value="$client->address ?? '-'" field="address" entityType="clients"
                            :entityId="$client->id" fieldType="textarea" />
                    </div>

                    <!-- Status & Settings Section -->
                    <div class="col-12 mt-4">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-gear me-2"></i>Status & Settings
                        </h5>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        {{-- <x-info-card
                        label="Status"
                        :badge="$client->is_active ? 'Active' : 'Inactive'"
                        :badgeColor="$client->is_active ? 'success' : 'secondary'" /> --}}
                        <x-editable-info-card label="Status" :value="$client->is_active ? '1' : '0'" :displayValue="$client->is_active ? 'Active' : 'Inactive'" :badge="$client->is_active ? 'Active' : 'Inactive'"
                            :badgeColor="$client->is_active ? 'success' : 'secondary'" field="is_active" entityType="clients" :entityId="$client->id" fieldType="select"
                            :options="['1' => 'Active', '0' => 'Inactive']" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Billing Frequency" :value="$client->billing_frequency ? ucfirst($client->billing_frequency) : 'Not set'" :badge="$client->billing_frequency ? ucfirst($client->billing_frequency) : 'Not set'"
                            badgeColor="info" field="billing_frequency" entityType="clients" :entityId="$client->id"
                            fieldType="select" :options="[
                                'weekly' => 'Weekly',
                                'bi-weekly' => 'Bi-Weekly',
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                            ]" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Agreed Weekly Hours" :value="$client->agreed_weekly_hours ? $client->agreed_weekly_hours . ' hours' : '-'" field="agreed_weekly_hours"
                            entityType="clients" :entityId="$client->id" fieldType="number" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Agreed Monthly Hours" :value="$client->agreed_monthly_hours ? $client->agreed_monthly_hours . ' hours' : '-'" field="agreed_monthly_hours"
                            entityType="clients" :entityId="$client->id" fieldType="number" />
                    </div>

                    <!-- Additional Information Section -->
                    <div class="col-12 mt-4">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Additional Information
                        </h5>
                    </div>
                    @if ($client->lead)
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="Converted From Lead" :value="$client->lead->name" :link="\App\Helpers\RouteHelper::url('leads.show', $client->lead)" />
                        </div>
                    @endif
                    <div class="col-md-6 col-lg-4">
                        <x-info-card label="Created" :value="$client->created_at->format('M d, Y h:i A')" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-info-card label="Last Updated" :value="$client->updated_at->format('M d, Y h:i A')" />
                    </div>
                    <div class="col-12">
                        <x-editable-info-card label="Notes" :value="$client->notes ?? '-'" field="notes" entityType="clients"
                            :entityId="$client->id" fieldType="textarea" />
                    </div>
                </div>
            </div>

            <!-- Assigned Staff Tab -->
            <div class="tab-pane fade" id="staff" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Assigned Staff</h5>
                </div>

                @if ($client->staff->count() > 0)
                    @foreach ($client->staff as $staff)
                        <div class="staff-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">
                                        <a href="{{ \App\Helpers\RouteHelper::url('staff.show', $staff) }}"
                                            class="text-decoration-none">
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
                                            @if ($staff->pivot->is_active)
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
                        <i class="bi bi-people icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No staff assigned yet</p>
                    </div>
                @endif
            </div>

            <!-- Service History Tab -->
            <div class="tab-pane fade" id="service" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Service History</h5>
                </div>

                @if ($client->timesheets->count() > 0)
                    @foreach ($client->timesheets->sortByDesc('work_date') as $timesheet)
                        <div class="service-history-item">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1">
                                        <i
                                            class="bi bi-calendar-event me-2"></i>{{ $timesheet->work_date->format('M d, Y') }}
                                    </h6>
                                    <p class="mb-1 text-muted">
                                        <i class="bi bi-person me-2"></i>{{ $timesheet->staff->name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="mb-1">
                                        <strong class="text-primary">{{ number_format($timesheet->hours_worked, 2) }}
                                            hours</strong>
                                    </div>
                                    @if ($timesheet->status === 'approved' || $timesheet->is_approved)
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($timesheet->status === 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>
                            </div>

                            @if ($timesheet->clock_in_time && $timesheet->clock_out_time)
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($timesheet->clock_in_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($timesheet->clock_out_time)->format('h:i A') }}
                                    </small>
                                </div>
                            @endif

                            @if ($timesheet->notes)
                                <div class="mb-2">
                                    <small class="text-muted">{{ $timesheet->notes }}</small>
                                </div>
                            @endif

                            @if ($timesheet->jobPhotos->count() > 0)
                                <div class="photo-gallery">
                                    @foreach ($timesheet->jobPhotos as $photo)
                                        <div class="photo-item">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Job Photo"
                                                class="job-photo"
                                                data-image-modal="{{ asset('storage/' . $photo->photo_path) }}"
                                                data-image-type="{{ $photo->photo_type }}"
                                                onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                            <span
                                                class="photo-badge bg-{{ $photo->photo_type == 'before' ? 'warning' : 'success' }}">
                                                {{ ucfirst($photo->photo_type) }}
                                            </span>
                                            @if ($photo->is_approved)
                                                <span class="badge bg-success photo-badge-position">
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
                        <i class="bi bi-clock-history icon-48px empty-state-icon-medium"></i>
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

                @if ($client->communications->count() > 0)
                    @foreach ($client->communications->sortByDesc('created_at') as $communication)
                        <div class="communication-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span
                                        class="badge bg-{{ $communication->type == 'email' ? 'primary' : ($communication->type == 'call' ? 'success' : 'info') }} me-2">
                                        {{ ucfirst($communication->type) }}
                                    </span>
                                    @if ($communication->subject)
                                        <strong>{{ $communication->subject }}</strong>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $communication->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <p class="mb-1">{{ $communication->message }}</p>
                            <small class="text-muted">
                                @if ($communication->user)
                                    by {{ $communication->user->name }}
                                @endif
                            </small>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-chat-dots icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No communications yet</p>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                            data-bs-target="#addCommunicationModal">
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

                @if ($client->documents->count() > 0)
                    @foreach ($client->documents as $document)
                        <div class="document-item">
                            <div>
                                <i class="bi bi-file-earmark me-2"></i>
                                <strong>{{ $document->name }}</strong>
                                <span class="badge bg-secondary ms-2">{{ ucfirst($document->document_type) }}</span>
                                <small class="text-muted ms-2">{{ $document->created_at->format('M d, Y') }}</small>
                            </div>
                            <a href="{{ \App\Helpers\RouteHelper::url('documents.download', $document) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No documents uploaded yet</p>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                            data-bs-target="#uploadDocumentModal">
                            <i class="bi bi-upload me-2"></i>Upload First Document
                        </button>
                    </div>
                @endif
            </div>

            <!-- Feedback Tab -->
            <div class="tab-pane fade" id="feedback" role="tabpanel">
                <h5 class="mb-4">Customer Feedback</h5>

                @if ($client->feedback->count() > 0)
                    @foreach ($client->feedback->sortByDesc('created_at') as $fb)
                        <div class="communication-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>Feedback</strong>
                                    <span
                                        class="badge bg-{{ $fb->rating >= 4 ? 'success' : ($fb->rating >= 3 ? 'warning' : 'danger') }} ms-2">
                                        {{ $fb->rating }}/5
                                    </span>
                                </div>
                                <small class="text-muted">{{ $fb->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            @if ($fb->message)
                                <p class="mb-1">{{ $fb->message }}</p>
                            @endif
                            @if ($fb->name)
                                <small class="text-muted">From: {{ $fb->name }}</small>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-star icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No feedback received yet</p>
                    </div>
                @endif
            </div>

            <!-- Invoices Tab -->
            <div class="tab-pane fade" id="invoices" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Invoices</h5>
                    <a href="{{ \App\Helpers\RouteHelper::url('invoices.create', ['client_id' => $client->id]) }}"
                        class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Invoice
                    </a>
                </div>

                @if ($client->invoices->count() > 0)
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
                                @foreach ($client->invoices->sortByDesc('created_at') as $invoice)
                                    <tr>
                                        <td>
                                            <a href="{{ \App\Helpers\RouteHelper::url('invoices.show', $invoice) }}"
                                                class="text-decoration-none">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $invoice->billing_period }}</td>
                                        <td>{{ number_format($invoice->hours_worked, 2) }}</td>
                                        <td>£{{ number_format($invoice->total_amount, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ \App\Helpers\RouteHelper::url('invoices.download', $invoice) }}"
                                                class="btn btn-sm btn-outline-primary" target="_blank">
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
                        <i class="bi bi-receipt icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No invoices yet</p>
                        <a href="{{ \App\Helpers\RouteHelper::url('invoices.create', ['client_id' => $client->id]) }}"
                            class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle me-2"></i>Create First Invoice
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Send Email Modal -->
    @if (auth()->user()->can('view admin dashboard'))
        <div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendEmailModalLabel">
                            <i class="bi bi-envelope me-2"></i>Send Email to Client
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="sendEmailForm" action="{{ \App\Helpers\RouteHelper::url('communications.store') }}">
                        @csrf
                        <input type="hidden" name="communicable_type" value="{{ get_class($client) }}">
                        <input type="hidden" name="communicable_id" value="{{ $client->id }}">
                        <input type="hidden" name="type" value="email">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="send_email_to" class="form-label">Email To <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="send_email_to" name="email_to"
                                    value="{{ $client->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="send_email_subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="send_email_subject" name="subject"
                                    value="Follow-up: {{ $client->company_name }}" placeholder="Enter email subject">
                            </div>
                            <div class="mb-3">
                                <label for="send_email_message" class="form-label">Message <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="send_email_message" name="message" rows="8" required
                                    placeholder="Enter your message...">Dear {{ $client->contact_person ?? $client->company_name }},

Thank you for your business. We would like to follow up with you regarding your account.

Best regards,
{{ auth()->user()->name }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>Send Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
                    <img id="modalImage" src="" alt="Job Photo" class="img-fluid modal-image"
                        onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/entity-details.js', 'resources/js/inline-edit.js'])
@endpush
