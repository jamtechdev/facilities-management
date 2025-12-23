@extends('layouts.app')

@section('title', 'Service History')

@push('styles')
    @vite(['resources/css/profile.css'])
    <style>
        /* Extra spacing for better breathing room */
        .timeline-item {
            margin-bottom: 4rem !important;
            /* Zyada space between entries */
        }

        .timeline-content {
            padding: 2rem !important;
            /* Card ke andar zyada space */
        }

        .job-photos {
            margin-top: 1.5rem;
        }

        .job-photo:hover {
            transform: scale(1.1);
            transition: transform 0.3s ease;
            z-index: 10;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="d-flex align-items-center gap-4">
                    <div class="profile-avatar"
                        style="width: 90px; height: 90px; font-size: 3rem; background: rgba(255,255,255,0.25); backdrop-filter: blur(12px); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        {{ strtoupper(substr($client->company_name ?? 'C', 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="client-greeting mb-2" style="font-size: 2.5rem; font-weight: 800;">
                            Service History
                        </h1>
                        <p class="client-subtitle mb-1" style="font-size: 1.25rem;">
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
                <div class="profile-card client-stat-card primary" style="border-radius: 20px; overflow: hidden;">
                    <div class="profile-card-header">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <i class="bi bi-clock-history" style="font-size: 1.8rem;"></i>
                            <h5 class="mb-0" style="font-size: 1.4rem; font-weight: 700;">Service Timeline</h5>
                        </div>
                        <span class="badge bg-success fs-5 px-4 py-2" style="border-radius: 50px; font-weight: 600;">
                            {{ $serviceHistory->count() }} Timesheet{{ $serviceHistory->count() != 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="profile-card-body pt-4">
                        @if ($serviceHistory->count() > 0)
                            <div class="timeline">
                                @foreach ($serviceHistory as $timesheet)
                                    <div class="timeline-item">
                                        <div class="timeline-date mb-3">
                                            <strong
                                                style="font-size: 1.1rem;">{{ $timesheet->created_at->format('d M Y') }}</strong>
                                            <small
                                                class="ms-3 text-muted">{{ $timesheet->created_at->format('h:i A') }}</small>
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
                                                    <h6 class="timeline-title mb-3" style="font-size: 1.25rem;">
                                                        Work Session Completed
                                                        @if ($timesheet->staff)
                                                            <br><small class="text-success" style="font-weight: 600;">
                                                                <i class="bi bi-person-check me-1"></i>
                                                                by {{ $timesheet->staff->name }}
                                                            </small>
                                                        @endif
                                                    </h6>

                                                    @if ($timesheet->notes)
                                                        <div class="bg-light p-3 rounded mb-4"
                                                            style="border-left: 4px solid #84c373;">
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
                                                                <i class="bi bi-camera-fill text-success"
                                                                    style="font-size: 1.5rem;"></i>
                                                                <div>
                                                                    <strong style="font-size: 1.1rem;">Job Photos</strong>
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
                                                                        class="rounded overflow-hidden shadow-lg d-block"
                                                                        style="border: 3px solid #fff;">
                                                                        <img src="{{ Storage::url($photoPath) }}"
                                                                            alt="Job photo" class="job-photo"
                                                                            style="width: 140px; height: 140px;">
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-muted mt-3">
                                                            <i class="bi bi-camera"></i> No photos attached for this session
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="text-end">
                                                    <span class="badge bg-success px-4 py-3"
                                                        style="font-size: 1rem; border-radius: 15px;">
                                                        <i class="bi bi-check2-all me-2" style="font-size: 1.2rem;"></i>
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
                                <i class="bi bi-inbox" style="font-size: 6rem; color: #dee2e6;"></i>
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
