@extends('layouts.app')

@section('title', 'Invoice Details')

@push('styles')
    @vite(['resources/css/entity-details.css', 'resources/css/common-styles.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Invoice Header -->
    <x-header-card
        :title="'Invoice #' . $invoice->invoice_number"
        :company="$invoice->client->company_name"
        :email="$invoice->client->email"
        :phone="$invoice->client->phone"
        type="client">
        <x-slot name="actions">
            @if(auth()->user()->can('edit invoices'))
                <button class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#sendInvoiceEmailModal">
                    <i class="bi bi-envelope me-2"></i>Send Email
                </button>
            @endif
            <a href="{{ \App\Helpers\RouteHelper::url('invoices.download', $invoice) }}" class="btn btn-light me-2">
                <i class="bi bi-download me-2"></i>Download PDF
            </a>
            <a href="{{ \App\Helpers\RouteHelper::url('invoices.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </x-slot>
    </x-header-card>

    <!-- Tabs Navigation -->
    <x-tab-navigation
        :tabs="[
            ['id' => 'info', 'label' => 'Information', 'icon' => 'bi-info-circle'],
            ['id' => 'emails', 'label' => 'Email History', 'icon' => 'bi-envelope', 'badge' => $invoice->communications->where('type', 'email')->count()],
        ]"
        id="invoiceTabs" />

    <!-- Tab Content -->
    <div class="tab-content" id="invoiceTabsContent">
        <!-- Information Tab -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="row g-3">
                <!-- Invoice Details Section -->
                <div class="col-12">
                    <h5 class="mb-3 text-muted fw-bold">
                        <i class="bi bi-receipt me-2"></i>Invoice Details
                    </h5>
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-editable-info-card
                        label="Invoice Number"
                        :value="$invoice->invoice_number"
                        field="invoice_number"
                        entityType="invoices"
                        :entityId="$invoice->id"
                        fieldType="text"
                        :editable="auth()->user()->can('edit invoices')" />
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-info-card label="Created Date" :value="$invoice->created_at->format('M d, Y h:i A')" />
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-editable-info-card
                        label="Due Date"
                        :value="$invoice->due_date ? $invoice->due_date->format('M d, Y') : '-'"
                        field="due_date"
                        entityType="invoices"
                        :entityId="$invoice->id"
                        fieldType="date"
                        :editable="auth()->user()->can('edit invoices')" />
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-info-card
                        label="Status"
                        :badge="ucfirst($invoice->status)"
                        :badgeColor="$invoice->status === 'paid' ? 'success' : ($invoice->status === 'sent' ? 'info' : ($invoice->status === 'unpaid' ? 'warning' : 'secondary'))" />
                </div>

                <!-- Billing Period Section -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 text-muted fw-bold">
                        <i class="bi bi-calendar-range me-2"></i>Billing Period
                    </h5>
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-editable-info-card
                        label="Start Date"
                        :value="$invoice->billing_period_start ? $invoice->billing_period_start->format('M d, Y') : '-'"
                        field="billing_period_start"
                        entityType="invoices"
                        :entityId="$invoice->id"
                        fieldType="date"
                        :editable="auth()->user()->can('edit invoices')" />
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-editable-info-card
                        label="End Date"
                        :value="$invoice->billing_period_end ? $invoice->billing_period_end->format('M d, Y') : '-'"
                        field="billing_period_end"
                        entityType="invoices"
                        :entityId="$invoice->id"
                        fieldType="date"
                        :editable="auth()->user()->can('edit invoices')" />
                </div>

                <!-- Client Information Section -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 text-muted fw-bold">
                        <i class="bi bi-building me-2"></i>Client Information
                    </h5>
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-info-card label="Company Name" :value="$invoice->client->company_name" />
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-info-card label="Contact Person" :value="$invoice->client->contact_person" />
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-info-card label="Email" :value="$invoice->client->email" :link="'mailto:' . $invoice->client->email" />
                </div>
                <div class="col-md-6 col-lg-4">
                    <x-info-card label="Phone" :value="$invoice->client->phone ?? '-'" :link="$invoice->client->phone ? 'tel:' . $invoice->client->phone : null" />
                </div>

                <!-- Invoice Items Section -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 text-muted fw-bold">
                        <i class="bi bi-list-check me-2"></i>Invoice Items
                    </h5>
                </div>
                <div class="col-12">
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
                                    <td>
                                        @if(auth()->user()->can('edit invoices'))
                                            <x-editable-info-card
                                                label=""
                                                :value="'Cleaning Services'"
                                                field="description"
                                                entityType="invoices"
                                                :entityId="$invoice->id"
                                                fieldType="text"
                                                :editable="true" />
                                        @else
                                            Cleaning Services
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(auth()->user()->can('edit invoices'))
                                            <x-editable-info-card
                                                label=""
                                                :value="number_format($invoice->total_hours, 2)"
                                                field="total_hours"
                                                entityType="invoices"
                                                :entityId="$invoice->id"
                                                fieldType="number"
                                                :editable="true" />
                                        @else
                                            {{ number_format($invoice->total_hours, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(auth()->user()->can('edit invoices'))
                                            <x-editable-info-card
                                                label=""
                                                :value="number_format($invoice->hourly_rate, 2)"
                                                field="hourly_rate"
                                                entityType="invoices"
                                                :entityId="$invoice->id"
                                                fieldType="number"
                                                :editable="true" />
                                        @else
                                            ${{ number_format($invoice->hourly_rate, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-end">$<span id="subtotal-display">{{ number_format($invoice->subtotal, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <td class="text-end" colspan="2">
                                        @if(auth()->user()->can('edit invoices'))
                                            <x-editable-info-card
                                                label=""
                                                :value="number_format($invoice->tax ?? 0, 2)"
                                                field="tax"
                                                entityType="invoices"
                                                :entityId="$invoice->id"
                                                fieldType="number"
                                                :editable="true" />
                                        @else
                                            ${{ number_format($invoice->tax ?? 0, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-end">${{ number_format($invoice->tax ?? 0, 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end" colspan="2"></td>
                                    <td class="text-end">
                                        <strong>$<span id="total-display">{{ number_format($invoice->total_amount, 2) }}</span></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 text-muted fw-bold">
                        <i class="bi bi-sticky me-2"></i>Notes
                    </h5>
                </div>
                <div class="col-12">
                    <x-editable-info-card
                        label=""
                        :value="$invoice->notes ?? '-'"
                        field="notes"
                        entityType="invoices"
                        :entityId="$invoice->id"
                        fieldType="textarea"
                        :editable="auth()->user()->can('edit invoices')" />
                </div>
            </div>
        </div>

        <!-- Email History Tab -->
        <div class="tab-pane fade" id="emails" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Email History</h5>
            </div>

            @if($invoice->communications->where('type', 'email')->count() > 0)
                @foreach($invoice->communications->where('type', 'email')->sortByDesc('created_at') as $communication)
                    <div class="communication-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-primary me-2">
                                    <i class="bi bi-envelope me-1"></i>Email
                                </span>
                                @if($communication->subject)
                                    <strong>{{ $communication->subject }}</strong>
                                @endif
                            </div>
                            <small class="text-muted">{{ $communication->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        @if($communication->email_to)
                            <p class="mb-1">
                                <strong>To:</strong> {{ $communication->email_to }}
                            </p>
                        @endif
                        @if($communication->message)
                            <p class="mb-1">{{ $communication->message }}</p>
                        @endif
                        <small class="text-muted">
                            @if($communication->user)
                                Sent by {{ $communication->user->name }}
                            @endif
                            @if($communication->is_sent)
                                <span class="badge bg-success ms-2">Sent</span>
                            @else
                                <span class="badge bg-warning ms-2">Pending</span>
                            @endif
                        </small>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-envelope icon-48px empty-state-icon-medium"></i>
                    <p class="text-muted mt-3">No emails sent yet</p>
                    @if(auth()->user()->can('edit invoices'))
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#sendInvoiceEmailModal">
                            <i class="bi bi-envelope me-2"></i>Send First Email
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Send Invoice Email Modal -->
@if(auth()->user()->can('edit invoices'))
<div class="modal fade" id="sendInvoiceEmailModal" tabindex="-1" aria-labelledby="sendInvoiceEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendInvoiceEmailModalLabel">
                    <i class="bi bi-envelope me-2"></i>Send Invoice via Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendInvoiceEmailForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="invoice_email_to" class="form-label">Email To <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="invoice_email_to" name="email_to" value="{{ $invoice->client->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="invoice_email_subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="invoice_email_subject" name="subject" value="Invoice #{{ $invoice->invoice_number }} - {{ $invoice->client->company_name }}" placeholder="Enter email subject">
                    </div>
                    <div class="mb-3">
                        <label for="invoice_email_message" class="form-label">Message</label>
                        <textarea class="form-control" id="invoice_email_message" name="message" rows="5" placeholder="Enter your message (optional)">Dear {{ $invoice->client->contact_person }},

Please find attached invoice #{{ $invoice->invoice_number }} for your review.

Thank you for your business!

Best regards,
{{ auth()->user()->name }}</textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        The invoice PDF will be attached to this email automatically.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i>Send Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
    @vite(['resources/js/inline-edit.js'])
    <script>
        // Auto-save status to draft when fields are edited
        $(document).ready(function() {
            if (typeof $ === 'undefined') {
                setTimeout(arguments.callee, 100);
                return;
            }

            // Track if invoice has been edited
            let invoiceEdited = false;

            // Monitor all editable fields
            $(document).on('click', '.btn-save-field', function() {
                invoiceEdited = true;

                // Auto-update status to draft after a short delay
                setTimeout(function() {
                    if (invoiceEdited) {
                        updateInvoiceStatus('draft', false);
                        invoiceEdited = false;
                    }
                }, 500);
            });

            function updateInvoiceStatus(status, showMessage) {
                axios.put('{{ \App\Helpers\RouteHelper::url("invoices.update-status", $invoice) }}', {
                    status: status
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .then(function(response) {
                    if (response.data.success && showMessage) {
                        if (typeof window.showToast !== 'undefined') {
                            window.showToast('success', 'Status updated to ' + status);
                        }
                    }
                })
                .catch(function(error) {
                    console.error('Failed to update status:', error);
                });
            }

            // Recalculate totals when hourly_rate, tax, or total_hours changes
            function recalculateTotals() {
                const hourlyRate = parseFloat($('.editable-info-card[data-field="hourly_rate"] .field-display').text().replace('$', '').replace(',', '')) || {{ $invoice->hourly_rate }};
                const totalHours = parseFloat($('.editable-info-card[data-field="total_hours"] .field-display').text().replace(',', '')) || {{ $invoice->total_hours }};
                const tax = parseFloat($('.editable-info-card[data-field="tax"] .field-display').text().replace('$', '').replace(',', '')) || {{ $invoice->tax ?? 0 }};

                const subtotal = totalHours * hourlyRate;
                const total = subtotal + tax;

                $('#subtotal-display').text(subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $('#total-display').text(total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            }

            // Recalculate when fields are saved
            $(document).on('click', '.btn-save-field', function() {
                setTimeout(recalculateTotals, 1000);
            });

            // Send Invoice Email Form
            $('#sendInvoiceEmailForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending...');

                axios.post('{{ \App\Helpers\RouteHelper::url("invoices.send-email", $invoice) }}', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('sendInvoiceEmailModal'));
                        if (modal) modal.hide();

                        // Show success message
                        if (typeof window.showToast !== 'undefined') {
                            window.showToast('success', 'Invoice sent successfully! Status updated to Sent.');
                        }

                        // Reload page
                        setTimeout(() => location.reload(), 1000);
                    }
                })
                .catch(function(error) {
                    const message = error.response?.data?.message || 'Failed to send invoice email';
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', message);
                    }
                    submitBtn.prop('disabled', false).html(originalText);
                });
            });
        });
    </script>
@endpush
