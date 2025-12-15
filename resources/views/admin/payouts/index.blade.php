@extends('layouts.app')

@section('title', 'Staff Payouts')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Staff Payouts</h1>
            <p class="text-muted">Calculate and generate payout reports</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Calculate Payout</h5>
                </div>
                <div class="card-body">
                    <form id="payoutForm">
                        @csrf
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Select Staff <span class="text-danger">*</span></label>
                            <select class="form-select" id="staff_id" name="staff_id" required>
                                <option value="">Choose a staff member...</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" data-rate="{{ $s->hourly_rate }}">
                                        {{ $s->name }} - ${{ number_format($s->hourly_rate, 2) }}/hr
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calculator me-2"></i>Calculate Payout
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="payoutResult" class="mt-4" style="display: none;">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Payout Calculation Result</h5>
                    </div>
                    <div class="card-body">
                        <div id="resultContent"></div>
                        <div class="mt-3">
                            <form id="downloadForm" method="GET" action="{{ route('admin.payouts.download') }}">
                                <input type="hidden" name="staff_id" id="download_staff_id">
                                <input type="hidden" name="start_date" id="download_start_date">
                                <input type="hidden" name="end_date" id="download_end_date">
                                <button type="submit" class="btn btn-info">
                                    <i class="bi bi-download me-2"></i>Download PDF Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('payoutForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Calculating...';

        try {
            const response = await fetch('{{ route("admin.payouts.calculate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                const result = data.data;
                document.getElementById('resultContent').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Staff:</strong> ${result.staff.name}</p>
                            <p class="mb-1"><strong>Period:</strong> ${result.start_date} to ${result.end_date}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Total Worked Hours:</strong> ${result.total_worked_hours} hours</p>
                            <p class="mb-1"><strong>Payable Hours:</strong> ${result.total_payable_hours} hours</p>
                            <p class="mb-1"><strong>Hourly Rate:</strong> $${result.hourly_rate}</p>
                            <h4 class="mt-3 text-success"><strong>Total Payout: $${result.payout}</strong></h4>
                        </div>
                    </div>
                `;
                document.getElementById('download_staff_id').value = result.staff.id;
                document.getElementById('download_start_date').value = result.start_date;
                document.getElementById('download_end_date').value = result.end_date;
                document.getElementById('payoutResult').style.display = 'block';
            } else {
                alert(data.message);
            }
        } catch (error) {
            alert('Failed to calculate payout: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>
@endpush
@endsection

