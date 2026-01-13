@extends('layouts.app')

@section('title', 'My Feedback')

@push('styles')
    @vite(['resources/css/profile.css', 'resources/css/client-dashboard.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/feedback.js'])
@endpush

@section('content')
    <div class="container-fluid feedback-section">
        <!-- Header -->
        <div class="client-dashboard-header mb-4">
            <div class="client-dashboard-header-content">
                <h1 class="client-greeting">My Feedback</h1>
                <p class="client-subtitle">View your submitted feedback and share your experience</p>
            </div>
        </div>

        <!-- Submit Feedback Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="service-card">
                    <div class="service-card-header">
                        <i class="bi bi-chat-square-text"></i>
                        <h5>Submit New Feedback</h5>
                    </div>
                    <div class="service-card-body">
                        <form id="feedbackForm" method="POST" action="{{ \App\Helpers\RouteHelper::url('feedback.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="timesheet_id" class="form-label">
                                        <i class="bi bi-clock-history me-1"></i>Select Work Session <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('timesheet_id') is-invalid @enderror" id="timesheet_id" name="timesheet_id" required>
                                        <option value="">Choose a completed work session...</option>
                                        @foreach($completedTimesheets as $timesheet)
                                            <option value="{{ $timesheet->id }}">
                                                {{ $timesheet->work_date->format('M d, Y') }} -
                                                {{ $timesheet->staff->name ?? 'Staff' }} -
                                                {{ $timesheet->clock_in_time->format('h:i A') }} to {{ $timesheet->clock_out_time->format('h:i A') }}
                                                ({{ number_format($timesheet->hours_worked, 2) }}h)
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Select the specific work session you want to provide feedback for</small>
                                    @error('timesheet_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($completedTimesheets->count() == 0)
                                        <div class="alert alert-info mt-2 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>
                                            No completed work sessions available yet. Feedback can only be given for completed work sessions.
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                                    <div class="rating-input">
                                        <input type="radio" name="rating" id="rating5" value="5" class="d-none" required>
                                        <label for="rating5" class="rating-star" data-rating="5"><i class="bi bi-star"></i></label>
                                        <input type="radio" name="rating" id="rating4" value="4" class="d-none">
                                        <label for="rating4" class="rating-star" data-rating="4"><i class="bi bi-star"></i></label>
                                        <input type="radio" name="rating" id="rating3" value="3" class="d-none">
                                        <label for="rating3" class="rating-star" data-rating="3"><i class="bi bi-star"></i></label>
                                        <input type="radio" name="rating" id="rating2" value="2" class="d-none">
                                        <label for="rating2" class="rating-star" data-rating="2"><i class="bi bi-star"></i></label>
                                        <input type="radio" name="rating" id="rating1" value="1" class="d-none">
                                        <label for="rating1" class="rating-star" data-rating="1"><i class="bi bi-star"></i></label>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="message" class="form-label">Your Feedback <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required minlength="10" placeholder="Please share your experience with our services..."></textarea>
                                    <small class="text-muted">Minimum 10 characters required</small>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i>Submit Feedback
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Your Feedback History -->
        <div class="row">
            <div class="col-12 mb-3">
                <h5 class="mb-0">Your Feedback History</h5>
            </div>
        </div>

        <div class="row">
            @if ($feedbacks->count() > 0)
                @foreach ($feedbacks as $feedback)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="feedback-card">
                            <div class="feedback-card-header">
                                <div>
                                    <strong>{{ $feedback->name ?? 'Anonymous' }}</strong>
                                    <span class="ms-2">({{ $feedback->company ?? 'No Company' }})</span>
                                </div>
                                <div class="feedback-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= ($feedback->rating ?? 0) ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="feedback-card-body">
                                @if($feedback->timesheet)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">
                                            <i class="bi bi-clock-history me-1"></i>
                                            Work Session: {{ $feedback->timesheet->work_date->format('M d, Y') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-person me-1"></i>
                                            Staff: {{ $feedback->timesheet->staff->name ?? 'N/A' }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            Hours: {{ number_format($feedback->timesheet->hours_worked, 2) }}h
                                        </small>
                                    </div>
                                @endif
                                <p class="feedback-message">{{ $feedback->message }}</p>
                                <p class="feedback-meta">
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

                <!-- Pagination -->
                @if($feedbacks->hasPages())
                    <div class="col-12 mt-4">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            @else
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-chat-square-text empty-state-icon-medium"></i>
                        <h4 class="mt-3 text-muted">No Feedback Submitted Yet</h4>
                        <p class="text-muted">Use the form above to share your feedback with us.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .rating-input {
            display: flex;
            gap: 5px;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .rating-star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-star:hover,
        .rating-star:hover ~ .rating-star {
            color: #ffc107;
        }

        .rating-input input[type="radio"]:checked ~ label,
        .rating-input input[type="radio"]:checked ~ label ~ label {
            color: #ffc107;
        }

        .rating-input label {
            margin: 0;
        }
    </style>

    <script>
        // Pass route to JS
        window.feedbackRoute = '{{ \App\Helpers\RouteHelper::url("feedback.store") }}';
    </script>
@endsection
