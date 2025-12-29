@extends('layouts.app')

@section('title', 'Before & After Gallery')

@push('styles')
@vite(['resources/css/profile.css'])
 @vite(['resources/css/client-dashboard.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    {{ strtoupper(substr($client->company_name ?? 'C', 0, 1)) }}
                </div>
                <div>
                    <h1 class="client-greeting mb-2">Before & After Gallery</h1>
                    <p class="client-subtitle mb-1">{{ $client->company_name }}</p>
                    <p class="client-subtitle opacity-80">
                        Witness the amazing transformation of your space
                    </p>
                </div>
            </div>
        </div>


        <!-- Gallery Cards -->
        <div class="row mt-4">
            <div class="col-lg-11 mx-auto">
                @if (count($photoPairs) > 0)
                    <div class="row g-4">
                        @foreach ($photoPairs as $pair)
                            <div class="col-md-6 col-lg-4">
                                <div class="pair-card">
                                    <div class="row g-0">
                                        <!-- Before -->
                                        <div class="col-6">
                                            <div class="photo-container">
                                                @if ($pair['before']->count() > 0)
                                                    @php
                                                        $firstBefore = $pair['before']->first();
                                                        $beforePath = 'job-photos/' . basename($firstBefore->file_path);
                                                    @endphp
                                                    <img src="{{ asset('storage/' . $beforePath) }}" alt="Before" onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                                    <div class="photo-label">Before</div>
                                                    @if ($pair['before']->count() > 1)
                                                        <div class="more-badge">+{{ $pair['before']->count() - 1 }}</div>
                                                    @endif
                                                @else
                                                    <div class="no-photo">
                                                        <i class="bi bi-image icon-3rem"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- After -->
                                        <div class="col-6">
                                            <div class="photo-container">
                                                @if ($pair['after']->count() > 0)
                                                    @php
                                                        $firstAfter = $pair['after']->first();
                                                        $afterPath = 'job-photos/' . basename($firstAfter->file_path);
                                                    @endphp
                                                    <img src="{{ asset('storage/' . $afterPath) }}" alt="After" onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                                    <div class="photo-label after-label">After</div>
                                                    @if ($pair['after']->count() > 1)
                                                        <div class="more-badge">+{{ $pair['after']->count() - 1 }}</div>
                                                    @endif
                                                @else
                                                    <div class="no-photo">
                                                        <i class="bi bi-image icon-3rem"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Footer Info -->
                                    <div class="session-info">
                                        <p class="fw-bold mb-1">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            {{ $pair['timesheet']->work_date->format('d M Y') }}
                                        </p>
                                        @if ($pair['timesheet']->staff)
                                            <p class="text-muted small mb-1">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $pair['timesheet']->staff->name }}
                                            </p>
                                        @endif
                                        <p class="text-success small fw-bold mt-2">
                                            <i class="bi bi-images me-2"></i>
                                            {{ $pair['before']->count() + $pair['after']->count() }}
                                            Photo{{ $pair['before']->count() + $pair['after']->count() != 1 ? 's' : '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 text-center">
                        {{ $timesheets->links() }}
                    </div>
                @else
                    <div class="text-center py-5 my-5">
                        <i class="bi bi-images empty-state-icon-large"></i>
                        <h3 class="text-muted mt-3">No Gallery Images Yet</h3>
                        <p class="text-muted">Before & After photos will appear here once our team uploads them.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
