@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Invoice #{{ $invoice->invoice_number }}</h1>
                        <p class="text-muted">{{ $invoice->client->company_name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.invoices.download', $invoice) }}" class="btn btn-info me-2">
                            <i class="bi bi-download me-2"></i>Download PDF
                        </a>
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Bill To:</h6>
                                <p class="mb-0"><strong>{{ $invoice->client->company_name }}</strong></p>
                                <p class="mb-0">{{ $invoice->client->contact_person }}</p>
                                <p class="mb-0">{{ $invoice->client->email }}</p>
                                <p class="mb-0">{{ $invoice->client->address }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h6 class="text-muted">Invoice Details:</h6>
                                <p class="mb-1"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                                <p class="mb-1"><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                                <p class="mb-1"><strong>Status:</strong>
                                    <span
                                        class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'unpaid' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6 class="mb-3">Billing Period</h6>
                            <p class="mb-0">
                                <strong>From:</strong> {{ $invoice->billing_period_start->format('M d, Y') }}
                                <strong>To:</strong> {{ $invoice->billing_period_end->format('M d, Y') }}
                            </p>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-end">Hours</th>
                                        <th class="text-end">Rate</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cleaning Services</td>
                                        <td class="text-end">{{ number_format($invoice->total_hours, 2) }}</td>
                                        <td class="text-end">${{ number_format($invoice->hourly_rate, 2) }}</td>
                                        <td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td>
                                    </tr>
                                    @if ($invoice->tax > 0)
                                        <tr>
                                            <td>Tax</td>
                                            <td class="text-end" colspan="2"></td>
                                            <td class="text-end">${{ number_format($invoice->tax, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr class="table-active">
                                        <td><strong>Total</strong></td>
                                        <td class="text-end" colspan="2"></td>
                                        <td class="text-end">
                                            <strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if ($invoice->notes)
                            <div class="mt-4">
                                <h6>Notes</h6>
                                <p class="text-muted">{{ $invoice->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <form id="statusForm">
                            @csrf
                            <div class="mb-3">
                                <label for="status" class="form-label">Update Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>Draft
                                    </option>
                                    <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Sent
                                    </option>
                                    <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="unpaid" {{ $invoice->status === 'unpaid' ? 'selected' : '' }}>Unpaid
                                    </option>
                                    <option value="overdue" {{ $invoice->status === 'overdue' ? 'selected' : '' }}>Overdue
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('statusForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

                try {
                    // const response = await fetch('{{ route('admin.invoices.update-status', $invoice) }}', {
                    //     method: 'PUT',
                    //     body: formData,
                    //     headers: {
                    //         'X-Requested-With': 'XMLHttpRequest',
                    //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    //     }
                    // });

                    const response = await fetch('{{ route('admin.invoices.update-status', $invoice) }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: document.getElementById('status').value
                        })
                    });


                    const data = await response.json();

                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    alert('Failed to update status: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        </script>
    @endpush
@endsection
