@extends('layouts.app')

@section('title', 'Staff Details')

@push('styles')
    @vite(['resources/css/entity-details.css', 'resources/css/common-styles.css'])
@endpush

@push('scripts')
    @vite(['resources/js/image-modal.js'])
@endpush

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="container-fluid">
        <!-- Staff Header -->
        <x-header-card :title="$staff->name" :email="$staff->email" :phone="$staff->mobile" type="lead">
            <x-slot name="actions">
                <a href="{{ \App\Helpers\RouteHelper::url('staff.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </x-slot>
        </x-header-card>

        <!-- Tabs Navigation -->
        <x-tab-navigation :tabs="[
            ['id' => 'info', 'label' => 'Information', 'icon' => 'bi-info-circle'],
            [
                'id' => 'clients',
                'label' => 'Assigned Clients',
                'icon' => 'bi-building',
                'badge' => $staff->clients->count(),
            ],
            [
                'id' => 'timesheets',
                'label' => 'Timesheets',
                'icon' => 'bi-clock-history',
                'badge' => $staff->timesheets->count(),
            ],
            [
                'id' => 'job-photos',
                'label' => 'Job Photos',
                'icon' => 'bi-camera',
                'badge' => $staff->jobPhotos->count(),
            ],
            [
                'id' => 'documents',
                'label' => 'Documents',
                'icon' => 'bi-file-earmark',
                'badge' => $staff->documents->count(),
            ],
            [
                'id' => 'leads',
                'label' => 'Assigned Leads',
                'icon' => 'bi-person-lines-fill',
                'badge' => $staff->leads->count(),
            ],
        ]" id="staffTabs" />

        <!-- Tab Content -->
        <div class="tab-content" id="staffTabsContent">
            <!-- Information Tab -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row g-3">
                    <!-- Personal Information Section -->
                    <div class="col-12">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-person-circle me-2"></i>Personal Information
                        </h5>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Name" :value="$staff->name" field="name" entityType="staff"
                            :entityId="$staff->id" fieldType="text" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Email" :value="$staff->email" :link="'mailto:' . $staff->email" field="email"
                            entityType="staff" :entityId="$staff->id" fieldType="email" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Mobile" :value="$staff->mobile ?? '-'" :link="$staff->mobile ? 'tel:' . $staff->mobile : null" field="mobile"
                            entityType="staff" :entityId="$staff->id" fieldType="text" />
                    </div>
                    <div class="col-12">
                        <x-editable-info-card label="Address" :value="$staff->address ?? '-'" field="address" entityType="staff"
                            :entityId="$staff->id" fieldType="textarea" />
                    </div>

                    <!-- Work Details Section -->
                    <div class="col-12 mt-4">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-briefcase me-2"></i>Work Details
                        </h5>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Hourly Rate" :value="$staff->hourly_rate ? '£' . number_format($staff->hourly_rate, 2) : '-'" field="hourly_rate" entityType="staff"
                            :entityId="$staff->id" fieldType="number" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Assigned Weekly Hours" :value="$staff->assigned_weekly_hours ? $staff->assigned_weekly_hours . ' hours' : '-'" field="assigned_weekly_hours"
                            entityType="staff" :entityId="$staff->id" fieldType="number" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Assigned Monthly Hours" :value="$staff->assigned_monthly_hours ? $staff->assigned_monthly_hours . ' hours' : '-'"
                            field="assigned_monthly_hours" entityType="staff" :entityId="$staff->id" fieldType="number" />
                    </div>
                    {{-- <div class="col-md-6 col-lg-4">
                    <x-info-card
                        label="Status"
                        :badge="$staff->is_active ? 'Active' : 'Inactive'"
                        :badgeColor="$staff->is_active ? 'success' : 'secondary'" />
                </div> --}}
                    <div class="col-md-6 col-lg-4">
                        <x-editable-info-card label="Status" :value="$staff->is_active ? 'Active' : 'Inactive'" field="is_active" entityType="staff"
                            :entityId="$staff->id" fieldType="select" :options="[1 => 'Active', 0 => 'Inactive']" :badge="$staff->is_active ? 'Active' : 'Inactive'"
                            :badgeColor="$staff->is_active ? 'success' : 'secondary'" />
                    </div>

                    <!-- Additional Information Section -->
                    <div class="col-12 mt-4">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Additional Information
                        </h5>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-info-card label="Created" :value="$staff->created_at->format('M d, Y h:i A')" />
                    </div>
                    @if ($staff->user)
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="User Account" :value="$staff->user->name" />
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Clients Tab -->
            <div class="tab-pane fade" id="clients" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Assigned Clients</h5>
                </div>

                @if ($staff->clients->count() > 0)
                    <div class="row g-3">
                        @foreach ($staff->clients as $client)
                            <div class="col-md-6 col-lg-4">
                                <div class="info-card">
                                    <div class="info-label">Company</div>
                                    <div class="info-value">
                                        <a href="{{ \App\Helpers\RouteHelper::url('clients.show', $client) }}"
                                            class="text-decoration-none">
                                            {{ $client->company_name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <x-info-card label="Weekly Hours" :value="$client->pivot->assigned_weekly_hours ?? '-'" />
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <x-info-card label="Monthly Hours" :value="$client->pivot->assigned_monthly_hours ?? '-'" />
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <x-info-card label="Status" :badge="$client->pivot->is_active ? 'Active' : 'Inactive'" :badgeColor="$client->pivot->is_active ? 'success' : 'secondary'" />
                            </div>
                            @if (!$loop->last)
                                <div class="col-12">
                                    <hr>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-building icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No clients assigned yet</p>
                    </div>
                @endif
            </div>

            <!-- Timesheets Tab -->
            <div class="tab-pane fade" id="timesheets" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Timesheet History</h5>
                </div>

                @if ($staff->timesheets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staff->timesheets->sortByDesc('clock_in_time') as $timesheet)
                                    <tr>
                                        <td>{{ $timesheet->clock_in_time->format('M d, Y') }}</td>
                                        <td>
                                            @if ($timesheet->client)
                                                <a href="{{ \App\Helpers\RouteHelper::url('clients.show', $timesheet->client) }}"
                                                    class="text-decoration-none">
                                                    {{ $timesheet->client->company_name }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $timesheet->clock_in_time->format('h:i A') }}</td>
                                        <td>{{ $timesheet->clock_out_time ? $timesheet->clock_out_time->format('h:i A') : '-' }}
                                        </td>
                                        <td>
                                            @if ($timesheet->clock_out_time)
                                                {{ number_format($timesheet->clock_in_time->diffInHours($timesheet->clock_out_time), 2) }}
                                                hrs
                                            @else
                                                <span class="badge bg-warning">In Progress</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No timesheets recorded yet</p>
                    </div>
                @endif
            </div>

            <!-- Job Photos Tab -->
            <div class="tab-pane fade" id="job-photos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Job Photos</h5>
                </div>

                @if ($staff->jobPhotos->count() > 0)
                    <div class="row g-3">
                        @foreach ($staff->jobPhotos as $photo)
                            @php
                                $photoPath = preg_replace('#^(storage|public)/#i', '', $photo->file_path ?? '');
                                $imageUrl = Storage::url($photoPath);
                            @endphp
                            <div class="col-md-4 col-lg-3">
                                <div class="document-item">
                                    <div class="text-center">
                                        <img src="{{ $imageUrl }}" alt="Job Photo" class="img-thumbnail"
                                            style="max-height: 200px; cursor: pointer; width: 100%; object-fit: cover;"
                                            onclick="openImageModal('{{ $imageUrl }}', '{{ $photo->client ? $photo->client->company_name : 'Job Photo' }}')"
                                            onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                        <p class="mt-2 mb-0 small">
                                            @if ($photo->client)
                                                <strong>{{ $photo->client->company_name }}</strong><br>
                                            @endif
                                            <span
                                                class="badge bg-{{ $photo->photo_type == 'before' ? 'warning' : 'success' }} me-1">
                                                {{ ucfirst($photo->photo_type) }}
                                            </span>
                                            @if ($photo->status == 'approved')
                                                <span class="badge bg-success me-1">
                                                    <i class="bi bi-check-circle"></i> Approved
                                                </span>
                                            @elseif($photo->status == 'rejected')
                                                <span class="badge bg-danger me-1">
                                                    <i class="bi bi-x-circle"></i> Rejected
                                                </span>
                                            @else
                                                <span class="badge bg-warning me-1">
                                                    <i class="bi bi-clock"></i> Pending
                                                </span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $photo->created_at->format('M d, Y') }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-camera icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No job photos uploaded yet</p>
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

                @if ($staff->documents->count() > 0)
                    @foreach ($staff->documents as $document)
                        <div class="document-item">
                            <div>
                                <i class="bi bi-file-earmark me-2"></i>
                                <strong>{{ $document->name }}</strong>
                                <span class="badge bg-secondary ms-2">{{ ucfirst($document->document_type) }}</span>
                                <small class="text-muted ms-2">{{ $document->created_at->format('M d, Y') }}</small>
                                @if ($document->uploadedBy)
                                    <small class="text-muted ms-2">by {{ $document->uploadedBy->name }}</small>
                                @endif
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

            <!-- Assigned Leads Tab -->
            <div class="tab-pane fade" id="leads" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Assigned Leads</h5>
                </div>

                @if ($staff->leads->count() > 0)
                    <div class="row g-3">
                        @foreach ($staff->leads as $lead)
                            <div class="col-md-6 col-lg-4">
                                <div class="info-card">
                                    <div class="info-label">Lead Name</div>
                                    <div class="info-value">
                                        <a href="{{ \App\Helpers\RouteHelper::url('leads.show', $lead) }}"
                                            class="text-decoration-none">
                                            {{ $lead->name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <x-info-card label="Company" :value="$lead->company ?? '-'" />
                            </div>
                            <div class="col-md-6 col-lg-4">
                                @php
                                    $stageColors = [
                                        'new_lead' => 'primary',
                                        'in_progress' => 'info',
                                        'qualified' => 'success',
                                        'not_qualified' => 'warning',
                                        'junk' => 'danger',
                                    ];
                                    $stageColor = $stageColors[$lead->stage] ?? 'secondary';
                                @endphp
                                <x-info-card label="Stage" :badge="ucfirst(str_replace('_', ' ', $lead->stage))" :badgeColor="$stageColor" />
                            </div>
                            @if (!$loop->last)
                                <div class="col-12">
                                    <hr>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-person-lines-fill icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No leads assigned yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upload Document Modal -->
    @include('superadmin.documents.modal', ['documentable' => $staff])

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Job Photo</h5>
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
    @vite(['resources/js/inline-edit.js'])
    <script>
        function openImageModal(imageSrc, title) {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModalLabel').textContent = title || 'Job Photo';
            modal.show();
        }
    </script>
@endpush
