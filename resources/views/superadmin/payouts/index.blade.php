@extends('layouts.app')

@section('title', 'Staff Payouts')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Payouts Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        <i class="bi bi-person-lines-fill icon-2-5rem"></i>
                    @endif
                </div>
                <div class="profile-info flex-grow-1">
                    <h1>Staff Payouts</h1>
                    <p>Calculate and generate payout reports</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Calculate Payout</h5>
                    </div>
                    <div class="card-body">
                        <form id="payoutForm" method="POST" action="javascript:void(0);">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="staff_id" class="form-label fw-semibold">Select Staff <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" id="staff_id" name="staff_id" required>
                                        <option value="">Choose a staff member...</option>
                                        @foreach ($staff as $s)
                                            <option value="{{ $s->id }}" data-rate="{{ $s->hourly_rate }}">
                                                {{ $s->name }} - ${{ number_format($s->hourly_rate, 2) }}/hr
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label fw-semibold">Start Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-lg" id="start_date"
                                        name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label fw-semibold">End Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-lg" id="end_date"
                                        name="end_date" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                                        <i class="bi bi-calculator me-2"></i>Calculate Payout
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="payoutResult" class="mt-4 payout-result" style="display: none;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Payout Calculation Result</h5>
                        </div>
                        <div class="card-body">
                            <div id="resultContent"></div>
                            <div class="mt-3">
                                {{-- <form id="downloadForm" method="GET" action="{{ route('admin.payouts.download') }}">
                                <input type="hidden" name="staff_id" id="download_staff_id">
                                <input type="hidden" name="start_date" id="download_start_date">
                                <input type="hidden" name="end_date" id="download_end_date">
                                <button type="submit" class="btn btn-info">
                                    <i class="bi bi-download me-2"></i>Download PDF Report
                                </button>
                            </form> --}}
                                <a id="downloadLink" class="btn btn-info" href="#" target="_blank">
                                    <i class="bi bi-download me-2"></i>Download PDF Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Pass routes to JS via data attributes - use RouteHelper for correct prefix
            window.payoutRoutes = {
                calculate: '{{ \App\Helpers\RouteHelper::url('payouts.calculate') }}',
                download: '{{ \App\Helpers\RouteHelper::url('payouts.download') }}'
            };
        </script>
        @vite(['resources/js/pages/payouts.js'])
    @endpush
@endsection
