@extends('layouts.app')

@section('title', 'Lead Details')

@push('styles')
    @vite(['resources/css/entity-details.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Lead Header -->
        <x-header-card :title="$lead->name" :company="$lead->company" :email="$lead->email" :phone="$lead->phone" type="lead">
            <x-slot name="actions">
                @if (auth()->user()->can('view admin dashboard'))
                    <button class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#sendEmailModal">
                        <i class="bi bi-envelope me-2"></i>Send Email
                    </button>
                @endif
                <a href="{{ \App\Helpers\RouteHelper::url('leads.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </x-slot>
        </x-header-card>

        <!-- Convert Switcher -->
        @php
            $user = auth()->user();
            $canConvert = $user->can('view admin dashboard') && $user->can('convert leads');
        @endphp
        <x-convert-switcher :lead="$lead" :canConvert="$canConvert" />

        <!-- Tabs Navigation -->
        <x-tab-navigation :tabs="[
            ['id' => 'info', 'label' => 'Information', 'icon' => 'bi-info-circle'],
            [
                'id' => 'communications',
                'label' => 'Communications',
                'icon' => 'bi-chat-dots',
                'badge' => $lead->communications->count(),
            ],
            [
                'id' => 'documents',
                'label' => 'Documents',
                'icon' => 'bi-file-earmark',
                'badge' => $lead->documents->count(),
            ],
            [
                'id' => 'followup',
                'label' => 'Follow-up Tasks',
                'icon' => 'bi-calendar-check',
                'badge' => $lead->followUpTasks->where('is_completed', false)->count(),
            ],
            ['id' => 'feedback', 'label' => 'Feedback', 'icon' => 'bi-star', 'badge' => $lead->feedback->count()],
        ]" id="leadTabs" />

        <!-- Tab Content -->
        <div class="tab-content" id="leadTabsContent">
            <!-- Information Tab -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row g-3">
                    <!-- Basic Information Section -->
                    <div class="col-12">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-person-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Name" :value="$lead->name" field="name" entityType="leads"
                            :entityId="$lead->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Email" :value="$lead->email" :link="'mailto:' . $lead->email" field="email"
                            entityType="leads" :entityId="$lead->id" fieldType="email" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Phone" :value="$lead->phone ?? '-'" :link="$lead->phone ? 'tel:' . $lead->phone : null" field="phone"
                            entityType="leads" :entityId="$lead->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Company" :value="$lead->company ?? '-'" field="company" entityType="leads"
                            :entityId="$lead->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Designation" :value="$lead->designation ?? '-'" field="designation" entityType="leads"
                            :entityId="$lead->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="City" :value="$lead->city ?? '-'" field="city" entityType="leads"
                            :entityId="$lead->id" fieldType="text" />
                    </div>

                    <!-- Lead Status Section -->
                    <div class="col-12 mt-4">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-tags me-2"></i>Lead Status
                        </h5>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Source" :value="$lead->source ?? '-'" field="source" entityType="leads"
                            :entityId="$lead->id" fieldType="select" :options="[
                                'Website' => 'Website',
                                'Referral' => 'Referral',
                                'Cold Call' => 'Cold Call',
                                'LinkedIn' => 'LinkedIn',
                                'Other' => 'Other',
                            ]" />
                    </div>
                    @php
                        $stageColors = [
                            'new_lead' => 'primary',
                            'in_progress' => 'info',
                            'qualified' => 'success',
                            'not_qualified' => 'warning',
                            'junk' => 'danger',
                        ];
                        $stageColor = $stageColors[$lead->stage] ?? 'secondary';
                        $stageOptions = [
                            'new_lead' => 'New Lead',
                            'in_progress' => 'In Progress',
                            'qualified' => 'Qualified',
                            'not_qualified' => 'Not Qualified',
                            'junk' => 'Junk',
                        ];
                    @endphp
                    {{-- <div class="col-md-6 col-lg-4">
                    <x-info-card
                        label="Stage"
                        :value="ucfirst(str_replace('_', ' ', $lead->stage))"
                        :badge="ucfirst(str_replace('_', ' ', $lead->stage))"
                        :badgeColor="$stageColor" />
                </div> --}}
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Stage" :value="$lead->stage" field="stage" entityType="leads"
                            :entityId="$lead->id" fieldType="select" :options="[
                                'new_lead' => 'New Lead',
                                'in_progress' => 'In Progress',
                                'qualified' => 'Qualified',
                                'not_qualified' => 'Not Qualified',
                                'junk' => 'Junk',
                            ]" />
                    </div>
                    {{-- <div class="col-md-6 col-lg-4">
                        <x-info-card label="Assigned Staff" :value="$lead->assignedStaff ? $lead->assignedStaff->name : 'Unassigned'" />
                    </div> --}}
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Assigned Staff" :value="$lead->assigned_staff_id" :displayValue="$lead->assignedStaff ? $lead->assignedStaff->name : 'Unassigned'"
                            field="assigned_staff_id" entityType="leads" :entityId="$lead->id" fieldType="select"
                            :options="$staff->pluck('name', 'id')->toArray()" />
                    </div>

                    <!-- Additional Information Section -->
                    <div class="col-12 mt-4">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Additional Information
                        </h5>
                    </div>
                    @if ($lead->convertedToClient)
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="Converted To Client" :value="$lead->convertedToClient->company_name" :link="\App\Helpers\RouteHelper::url('clients.show', $lead->convertedToClient)" />
                        </div>
                    @endif
                    <div class="col-md-6 col-lg-4">
                        <x-info-card label="Created" :value="$lead->created_at->format('M d, Y h:i A')" />
                    </div>
                    @if ($lead->user)
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="Created By" :value="$lead->user->name" />
                        </div>
                    @endif
                    @if ($lead->converted_at)
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="Converted At" :value="$lead->converted_at->format('M d, Y h:i A')" />
                        </div>
                    @endif
                    <div class="col-12">
                        <x-editable-info-card label="Notes" :value="$lead->notes ?? '-'" field="notes" entityType="leads"
                            :entityId="$lead->id" fieldType="textarea" />
                    </div>
                </div>
            </div>

            <!-- Communications Tab -->
            <div class="tab-pane fade" id="communications" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Communication History</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommunicationModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Communication
                    </button>
                </div>

                @if ($lead->communications->count() > 0)
                    @foreach ($lead->communications->sortByDesc('created_at') as $communication)
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

                @if ($lead->documents->count() > 0)
                    @foreach ($lead->documents as $document)
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

            <!-- Follow-up Tasks Tab -->
            <div class="tab-pane fade" id="followup" role="tabpanel">
                <h5 class="mb-4">Automated Follow-up Tasks</h5>

                @if ($lead->followUpTasks->count() > 0)
                    @foreach ($lead->followUpTasks->sortBy('reminder_day') as $task)
                        <div class="task-item {{ $task->is_completed ? 'completed' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="bi bi-calendar-event me-2"></i>Day {{ $task->reminder_day }} Reminder
                                    </h6>
                                    <p class="mb-1">{{ $task->suggestion }}</p>
                                    <small class="text-muted">
                                        Due: {{ $task->due_date->format('M d, Y') }}
                                        @if ($task->due_date->isPast() && !$task->is_completed)
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        @endif
                                    </small>
                                </div>
                                @if ($task->is_completed)
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-check icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No follow-up tasks</p>
                    </div>
                @endif
            </div>

            <!-- Feedback Tab -->
            <div class="tab-pane fade" id="feedback" role="tabpanel">
                <h5 class="mb-4">Customer Feedback</h5>

                @if ($lead->feedback->count() > 0)
                    @foreach ($lead->feedback->sortByDesc('created_at') as $fb)
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
                            @if ($fb->comment)
                                <p class="mb-1">{{ $fb->comment }}</p>
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
        </div>
    </div>

    <!-- Send Email Modal (Admin/SuperAdmin Only) -->
    @if (auth()->user()->can('view admin dashboard'))
        <div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendEmailModalLabel">
                            <i class="bi bi-envelope me-2"></i>Send Email to Lead
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="sendEmailForm" action="{{ \App\Helpers\RouteHelper::url('communications.store') }}">
                        @csrf
                        <input type="hidden" name="communicable_type" value="{{ get_class($lead) }}">
                        <input type="hidden" name="communicable_id" value="{{ $lead->id }}">
                        <input type="hidden" name="type" value="email">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="send_email_to" class="form-label">Email To <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="send_email_to" name="email_to"
                                    value="{{ $lead->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="send_email_subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="send_email_subject" name="subject"
                                    value="Follow-up: {{ $lead->company ?? $lead->name }}"
                                    placeholder="Enter email subject">
                            </div>
                            <div class="mb-3">
                                <label for="send_email_message" class="form-label">Message <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="send_email_message" name="message" rows="8" required
                                    placeholder="Enter your message...">Dear {{ $lead->name }},

Thank you for your interest in our services. We would like to follow up with you regarding your inquiry.

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
    @include('superadmin.communications.modal', ['communicable' => $lead])

    <!-- Upload Document Modal -->
    @include('superadmin.documents.modal', ['documentable' => $lead])

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

@push('styles')
    @vite(['resources/css/entity-details.css', 'resources/css/common-styles.css'])
@endpush

@push('scripts')
    <script>
        // Pass routes to JS (only once per page)
        if (typeof window.convertLeadRoute === 'undefined') {
            window.convertLeadRoute = '{{ \App\Helpers\RouteHelper::url('leads.convert', $lead) }}';
        }
    </script>
    @vite(['resources/js/entity-details.js', 'resources/js/image-modal.js', 'resources/js/inline-edit.js'])
@endpush
