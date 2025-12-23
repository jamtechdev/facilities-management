@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Create Invoice</h1>
            <p class="text-muted">Generate a new invoice for a client</p>
        </div>
    </div>

    <div id="alert-container"></div>

    <div class="row">
        <div class="col-12">
            <div class="form-card">
                <div class="form-card-header">
                    <h5><i class="bi bi-receipt me-2"></i>Invoice Information</h5>
                </div>
                <div class="form-card-body">
                    <form id="invoiceForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="client_id" class="form-label">
                                    <i class="bi bi-building me-1"></i>Client <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Select a client...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="hourly_rate" class="form-label">
                                    <i class="bi bi-currency-pound me-1"></i>Hourly Rate <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label for="billing_period_start" class="form-label">
                                    <i class="bi bi-calendar-event me-1"></i>Billing Period Start <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="billing_period_start" name="billing_period_start" required>
                            </div>
                            <div class="col-md-6">
                                <label for="billing_period_end" class="form-label">
                                    <i class="bi bi-calendar-check me-1"></i>Billing Period End <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="billing_period_end" name="billing_period_end" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tax" class="form-label">
                                    <i class="bi bi-percent me-1"></i>Tax Amount
                                </label>
                                <input type="number" step="0.01" class="form-control" id="tax" name="tax" value="0" placeholder="0.00">
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-sticky me-1"></i>Notes
                                </label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about this invoice..."></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Create Invoice
                                    </button>
                                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('invoiceForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

        try {
            const response = await fetch('{{ route("admin.invoices.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                showAlert('danger', data.message || 'Failed to create invoice');
            }
        } catch (error) {
            showAlert('danger', 'Failed to create invoice: ' + (error.message || 'Unknown error'));
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    function showAlert(type, message) {
        if (typeof showToast !== 'undefined') {
            showToast(type, message);
        } else if (typeof toastr !== 'undefined') {
            const toastType = type === 'danger' ? 'error' : type;
            toastr[toastType](message);
        } else {
            alert(message);
        }
    }
</script>
@endpush
@endsection

