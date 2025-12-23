@extends('layouts.app')

@section('title', 'Client Documents')

@push('styles')
    @vite(['resources/css/profile.css'])
    @vite(['resources/css/client-dashboard.css'])
@endpush

@section('content')
    <div class="container-fluid document-section">
        <div class="profile-header mb-4">
            <div class="client-dashboard-header-content">
                <h1 class="client-greeting">Client Documents</h1>
                <p class="client-subtitle">All uploaded files related to your account</p>
            </div>
        </div>

        <div class="row gy-4">
            @forelse($documents as $doc)
                <div class="col-md-4">
                    <div class="document-card">
                        <div class="document-title">{{ $doc->name }}</div>
                        <div class="document-meta">
                            Type: {{ strtoupper($doc->document_type) }} |
                            Format: {{ $doc->file_type ?? 'Unknown' }} |
                            Size: {{ number_format($doc->file_size / 1024, 2) }} KB
                        </div>
                        @if ($doc->description)
                            <p class="document-description">{{ $doc->description }}</p>
                        @endif
                        <div class="document-actions">
                            <a href="{{ asset('storage/' . $doc->file_path) }}" download>
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h4 class="mt-3 text-muted">No Documents Found</h4>
                    <p class="text-muted">Uploaded documents will appear here once available.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
