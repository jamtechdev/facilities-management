@extends('layouts.app')

@section('title', 'Service History')

@push('styles')
    @vite(['resources/css/profile.css', 'resources/css/common-styles.css'])
@endpush

@push('scripts')
    @vite(['resources/js/image-modal.js'])
@endpush

@section('content')
    <div class="container-fluid">
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="d-flex align-items-center gap-4">
                    <div class="profile-avatar avatar-large">
                        {{ strtoupper(substr($client->company_name ?? 'C', 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="client-greeting mb-2 heading-2-5rem">
                            Service History
                        </h1>
                        <p class="client-subtitle mb-1 subtitle-1-25rem">
                            {{ $client->company_name }}
                        </p>
                        <p class="client-subtitle opacity-80">
                            Complete record of all work performed by our team
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-11 mx-auto">
                <!-- Main Timeline Card with Green Accent -->
                <div class="profile-card client-stat-card primary card-rounded-20">
                    <div class="profile-card-header">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <i class="bi bi-clock-history timeline-icon-1-8rem"></i>
                            <h5 class="mb-0 timeline-heading-1-4rem">Service Timeline</h5>
                        </div>
                        <span class="badge bg-success fs-5 px-4 py-2 badge-large">
                            {{ $serviceHistory->count() }} Timesheet{{ $serviceHistory->count() != 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="profile-card-body pt-4">
                        @if ($serviceHistory->count() > 0)
                            <div class="timeline">
                                @foreach ($serviceHistory as $timesheet)
                                    <div class="timeline-item">
                                        <div class="timeline-date mb-3">
                                            <strong class="timeline-date-text">{{ $timesheet->created_at->format('d M Y') }}</strong>
                                            <small class="ms-3 text-muted">{{ $timesheet->created_at->format('h:i A') }}</small>
                                            @if ($timesheet->hours)
                                                <span class="ms-3 service-hours-badge">
                                                    <i class="bi bi-clock-fill me-2"></i>
                                                    <strong>{{ $timesheet->hours }} hours</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start gap-4">
                                                <div class="flex-grow-1">
                                                    <h6 class="timeline-title mb-3">
                                                        Work Session Completed
                                                        @if ($timesheet->staff)
                                                            <br><small class="text-success text-weight-600">
                                                                <i class="bi bi-person-check me-1"></i>
                                                                by {{ $timesheet->staff->name }}
                                                            </small>
                                                        @endif
                                                    </h6>

                                                    @if ($timesheet->notes)
                                                        <div class="bg-light p-3 rounded mb-4 timeline-note-box">
                                                            <p class="mb-0 text-dark">{{ $timesheet->notes }}</p>
                                                        </div>
                                                    @else
                                                        <p class="text-muted mb-4"><em>No additional notes for this
                                                                session.</em></p>
                                                    @endif

                                                    <!-- Job Photos Section -->
                                                    @if ($timesheet->jobPhotos->count() > 0)
                                                        <div class="job-photos">
                                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                                <i class="bi bi-camera-fill text-success timeline-icon-1-5rem"></i>
                                                                <div>
                                                                    <strong class="timeline-date-text">Job Photos</strong>
                                                                    <span class="service-photo-badge success ms-2">
                                                                        {{ $timesheet->jobPhotos->count() }}
                                                                        photo{{ $timesheet->jobPhotos->count() != 1 ? 's' : '' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-3">
                                                                @foreach ($timesheet->jobPhotos as $photo)
                                                                    @php
                                                                        $photoPath = preg_replace(
                                                                            '#^(storage|public)/#i',
                                                                            '',
                                                                            $photo->file_path ?? ($photo->path ?? ''),
                                                                        );
                                                                    @endphp
                                                                    <a href="{{ Storage::url($photoPath) }}"
                                                                        target="_blank"
                                                                        class="rounded overflow-hidden shadow-lg d-block photo-border-white"
                                                                        data-image-modal="{{ Storage::url($photoPath) }}"
                                                                        data-image-type="{{ $photo->photo_type ?? 'job' }}">
                                                                        <img src="{{ Storage::url($photoPath) }}"
                                                                            alt="Job photo" class="job-photo photo-size-140" onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-muted mt-3">
                                                            <i class="bi bi-camera"></i> No photos attached for this session
                                                        </div>
                                                    @endif

                                                    <!-- Customer Feedback Section -->
                                                    @if ($timesheet->feedback->count() > 0)
                                                        <div class="customer-feedback mt-4">
                                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                                <i class="bi bi-chat-left-text-fill text-primary timeline-icon-1-5rem"></i>
                                                                <div>
                                                                    <strong class="timeline-date-text">Customer Feedback</strong>
                                                                    <span class="service-photo-badge primary ms-2">
                                                                        {{ $timesheet->feedback->count() }}
                                                                        feedback{{ $timesheet->feedback->count() != 1 ? 's' : '' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            @foreach ($timesheet->feedback as $feedback)
                                                                <div class="bg-light p-3 rounded mb-2 timeline-note-box">
                                                                    @if ($feedback->rating)
                                                                        <div class="mb-2">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                <i class="bi bi-star{{ $i <= $feedback->rating ? '-fill' : '' }} text-warning"></i>
                                                                            @endfor
                                                                        </div>
                                                                    @endif
                                                                    <p class="mb-0 text-dark">{{ $feedback->message }}</p>
                                                                    @if ($feedback->name)
                                                                        <small class="text-muted">— {{ $feedback->name }}</small>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="text-end">
                                                    <span class="badge bg-success px-4 py-3 badge-completed">
                                                        <i class="bi bi-check2-all me-2 timeline-icon-1-2rem"></i>
                                                        Completed
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 my-5">
                                <i class="bi bi-inbox empty-state-icon-large"></i>
                                <h3 class="text-muted mt-4">No Service Records Yet</h3>
                                <p class="text-muted lead">Your service history will appear here once our team begins work.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
