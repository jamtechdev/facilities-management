@extends('layouts.app')

@section('title', 'Client Feedback')

@push('styles')
    @vite(['resources/css/profile.css'])
    @vite(['resources/css/client-dashboard.css'])
@endpush

@section('content')
    <div class="container-fluid feedback-section">
        <!-- Header -->
        <div class="client-dashboard-header mb-4">
            <div class="client-dashboard-header-content">
                <h1 class="client-greeting">Client Feedback</h1>
                <p class="client-subtitle">See what our clients are saying</p>
            </div>
        </div>

        <!-- Feedback Grid -->
        <div class="row">
            @if ($feedbacks->count() > 0)
                @foreach ($feedbacks as $feedback)
                    <div class="col-md-4"> <!-- 3 cards per row -->
                        <div class="feedback-card">
                            <div class="feedback-card-header">
                                <div>
                                    <strong>{{ $feedback->name ?? 'Anonymous' }}</strong>
                                    <span class="ms-2">({{ $feedback->company ?? 'No Company' }})</span>
                                </div>
                                <div class="feedback-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $feedback->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="feedback-card-body">
                                <p class="feedback-message">{{ $feedback->message }}</p>
                                <p class="feedback-meta">
                                    <i class="bi bi-envelope me-1"></i> {{ $feedback->email }} |
                                    <i class="bi bi-calendar3 me-1"></i> {{ $feedback->created_at->format('d M Y') }}
                                    @if ($feedback->is_processed)
                                        <span class="badge bg-success ms-2">Processed</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Pending</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

            @else
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-text" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h4 class="mt-3 text-muted">No Feedback Yet</h4>
                    <p class="text-muted">Client feedback will appear here once submitted.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
