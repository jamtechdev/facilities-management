@extends('layouts.app')

@section('title', 'Staff Details')

@push('styles')
    @vite(['resources/css/entity-details.css', 'resources/css/common-styles.css', 'resources/css/client-dashboard.css'])
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
                    <i class="bi bi-arrow-left me-2"></i>Back to Staff
                </a>
            </x-slot>
        </x-header-card>

        <!-- Tabs Navigation -->
        <x-tab-navigation :tabs="[
            ['id' => 'info', 'label' => 'Information', 'icon' => 'bi-info-circle'],
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
                        <x-info-card label="Name" :value="$staff->name" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-info-card label="Email" :value="$staff->email" :link="'mailto:' . $staff->email" />
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-info-card label="Mobile" :value="$staff->mobile ?? '-'" :link="$staff->mobile ? 'tel:' . $staff->mobile : null" />
                    </div>
                    @if ($staff->address)
                        <div class="col-12">
                            <x-info-card label="Address" :value="$staff->address" />
                        </div>
                    @endif

                    <!-- Work Details Section -->
                    <div class="col-12 mt-4">
                        <h5 class="mb-3 text-muted fw-bold">
                            <i class="bi bi-briefcase me-2"></i>Assignment Details (For Your Account)
                        </h5>
                    </div>
                    @if ($assignment)
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="Hourly Rate" :value="$staff->hourly_rate ? '£' . number_format($staff->hourly_rate, 2) : '-'" />
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="Assigned Weekly Hours (To You)" :value="$assignment->pivot->assigned_weekly_hours
                                ? $assignment->pivot->assigned_weekly_hours . ' hours'
                                : '-'" />
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="Assigned Monthly Hours (To You)" :value="$assignment->pivot->assigned_monthly_hours
                                ? $assignment->pivot->assigned_monthly_hours . ' hours'
                                : '-'" />
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <x-info-card label="Assignment Status" :badge="$assignment->pivot->is_active ? 'Active' : 'Inactive'" :badgeColor="$assignment->pivot->is_active ? 'success' : 'secondary'" />
                        </div>
                        @if ($assignment->pivot->assignment_start_date)
                            <div class="col-md-6 col-lg-4">
                                {{-- <x-info-card label="Assignment Start Date" :value="$assignment->pivot->assignment_start_date->format('M d, Y')" /> --}}
                                <x-info-card label="Assignment Start Date" :value="$assignment->pivot->assignment_start_date
                                    ? \Carbon\Carbon::parse($assignment->pivot->assignment_start_date)->format('M d, Y')
                                    : '-'" />
                            </div>
                        @endif
                        @if ($assignment->pivot->assignment_end_date)
                            <div class="col-md-6 col-lg-4">
                                <x-info-card label="Assignment End Date" :value="$assignment->pivot->assignment_end_date->format('M d, Y')" />
                            </div>
                        @endif
                    @endif

                    <div class="col-md-6 col-lg-4">
                        <x-info-card label="Staff Status" :badge="$staff->is_active ? 'Active' : 'Inactive'" :badgeColor="$staff->is_active ? 'success' : 'secondary'" />
                    </div>
                </div>
            </div>

            <!-- Timesheets Tab -->
            <div class="tab-pane fade" id="timesheets" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Timesheet History (For Your Account)</h5>
                </div>

                @if ($staff->timesheets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staff->timesheets as $timesheet)
                                    <tr>
                                        <td>{{ $timesheet->clock_in_time->format('M d, Y') }}</td>
                                        <td>{{ $timesheet->clock_in_time->format('h:i A') }}</td>
                                        <td>{{ $timesheet->clock_out_time ? $timesheet->clock_out_time->format('h:i A') : '-' }}
                                        </td>
                                        <td>
                                            @if ($timesheet->clock_out_time && $timesheet->hours_worked)
                                                {{ number_format($timesheet->hours_worked, 2) }} hrs
                                            @elseif($timesheet->clock_out_time)
                                                {{ number_format($timesheet->clock_in_time->diffInHours($timesheet->clock_out_time), 2) }}
                                                hrs
                                            @else
                                                <span class="badge bg-warning">In Progress</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($timesheet->status === 'approved' || $timesheet->is_approved)
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($timesheet->status === 'completed')
                                                <span class="badge bg-info">Completed</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
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
                        <p class="text-muted mt-3">No timesheets recorded yet for your account</p>
                    </div>
                @endif
            </div>

            <!-- Job Photos Tab -->
            <div class="tab-pane fade" id="job-photos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Job Photos (For Your Account)</h5>
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
                                            onclick="openImageModal('{{ $imageUrl }}', 'Job Photo - {{ $staff->name }}')"
                                            onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                        <p class="mt-2 mb-0 small">
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
                        <p class="text-muted mt-3">No job photos uploaded yet for your account</p>
                    </div>
                @endif
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Documents</h5>
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
                            <a href="{{ asset('storage/' . $document->file_path) }}" class="btn btn-sm btn-outline-primary"
                                download target="_blank">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark icon-48px empty-state-icon-medium"></i>
                        <p class="text-muted mt-3">No documents uploaded yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

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
    <script>
        function openImageModal(imageSrc, title) {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModalLabel').textContent = title || 'Job Photo';
            modal.show();
        }
    </script>
@endpush
