@extends('layouts.app')

@section('title', 'Lead Details')

@push('styles')
    @vite(['resources/css/entity-details.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Lead Header -->
    <x-header-card 
        :title="$lead->name"
        :company="$lead->company"
        :email="$lead->email"
        :phone="$lead->phone"
        type="lead">
        <x-slot name="actions">
            <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-light me-2">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </x-slot>
    </x-header-card>

    <!-- Convert Switcher -->
    @php
        $user = auth()->user();
        $canConvert = $user->hasRole('SuperAdmin') || $user->can('convert leads');
    @endphp
    <x-convert-switcher :lead="$lead" :canConvert="$canConvert" />

    <!-- Tabs Navigation -->
    <x-tab-navigation 
        :tabs="[
            ['id' => 'info', 'label' => 'Information', 'icon' => 'bi-info-circle'],
            ['id' => 'communications', 'label' => 'Communications', 'icon' => 'bi-chat-dots', 'badge' => $lead->communications->count()],
            ['id' => 'documents', 'label' => 'Documents', 'icon' => 'bi-file-earmark', 'badge' => $lead->documents->count()],
            ['id' => 'followup', 'label' => 'Follow-up Tasks', 'icon' => 'bi-calendar-check', 'badge' => $lead->followUpTasks->where('is_completed', false)->count()],
            ['id' => 'feedback', 'label' => 'Feedback', 'icon' => 'bi-star', 'badge' => $lead->feedback->count()]
        ]"
        id="leadTabs" />

    <!-- Tab Content -->
    <div class="tab-content" id="leadTabsContent">
        <!-- Information Tab -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="row g-3">
                <x-info-card label="Name" :value="$lead->name" />
                <x-info-card label="Email" :value="$lead->email" :link="'mailto:' . $lead->email" />
                <x-info-card label="Company" :value="$lead->company ?? '-'" />
                <x-info-card label="Designation" :value="$lead->designation ?? '-'" />
                <x-info-card label="Phone" :value="$lead->phone ?? '-'" :link="$lead->phone ? 'tel:' . $lead->phone : null" />
                <x-info-card label="City" :value="$lead->city ?? '-'" />
                <x-info-card 
                    label="Source" 
                    :badge="$lead->source ?? null" 
                    badgeColor="info" />
                @php
                    $stageColors = [
                        'new_lead' => 'primary',
                        'in_progress' => 'info',
                        'qualified' => 'success',
                        'not_qualified' => 'warning',
                        'junk' => 'danger'
                    ];
                    $stageColor = $stageColors[$lead->stage] ?? 'secondary';
                @endphp
                <x-info-card 
                    label="Stage" 
                    :badge="ucfirst(str_replace('_', ' ', $lead->stage))" 
                    :badgeColor="$stageColor" />
                <x-info-card 
                    label="Assigned Staff" 
                    :value="$lead->assignedStaff ? $lead->assignedStaff->name : 'Unassigned'" />
                @if($lead->convertedToClient)
                    <x-info-card 
                        label="Converted To Client" 
                        :value="$lead->convertedToClient->company_name" 
                        :link="route('admin.clients.show', $lead->convertedToClient)" />
                @endif
                @if($lead->notes)
                    <div class="col-12">
                        <x-info-card label="Notes" :value="$lead->notes" />
                    </div>
                @endif
                <x-info-card label="Created" :value="$lead->created_at->format('M d, Y h:i A')" />
                @if($lead->converted_at)
                    <x-info-card label="Converted At" :value="$lead->converted_at->format('M d, Y h:i A')" />
                @endif
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
            
            @if($lead->communications->count() > 0)
                @foreach($lead->communications->sortByDesc('created_at') as $communication)
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
            
            @if($lead->documents->count() > 0)
                @foreach($lead->documents as $document)
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

        <!-- Follow-up Tasks Tab -->
        <div class="tab-pane fade" id="followup" role="tabpanel">
            <h5 class="mb-4">Automated Follow-up Tasks</h5>
            
            @if($lead->followUpTasks->count() > 0)
                @foreach($lead->followUpTasks->sortBy('reminder_day') as $task)
                    <div class="task-item {{ $task->is_completed ? 'completed' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <i class="bi bi-calendar-event me-2"></i>Day {{ $task->reminder_day }} Reminder
                                </h6>
                                <p class="mb-1">{{ $task->suggestion }}</p>
                                <small class="text-muted">
                                    Due: {{ $task->due_date->format('M d, Y') }}
                                    @if($task->due_date->isPast() && !$task->is_completed)
                                        <span class="badge bg-danger ms-2">Overdue</span>
                                    @endif
                                </small>
                            </div>
                            @if($task->is_completed)
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-check" style="font-size: 48px; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No follow-up tasks</p>
                </div>
            @endif
        </div>

        <!-- Feedback Tab -->
        <div class="tab-pane fade" id="feedback" role="tabpanel">
            <h5 class="mb-4">Customer Feedback</h5>
            
            @if($lead->feedback->count() > 0)
                @foreach($lead->feedback->sortByDesc('created_at') as $fb)
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
                        @if($fb->comment)
                            <p class="mb-1">{{ $fb->comment }}</p>
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
    </div>
</div>

<!-- Add Communication Modal -->
@include('admin.communications.modal', ['communicable' => $lead])

<!-- Upload Document Modal -->
@include('admin.documents.modal', ['documentable' => $lead])

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
