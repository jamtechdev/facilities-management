@extends('layouts.app')

@section('title', 'Client Invoices')

@push('styles')
@vite(['resources/css/profile.css'])
@vite(['resources/css/client-dashboard.css'])
@endpush

@section('content')
<div class="container-fluid invoice-section">
    <div class="client-dashboard-header mb-4">
        <div class="client-dashboard-header-content">
            <h1 class="client-greeting">Invoices</h1>
            <p class="client-subtitle">All billing records for your account</p>
        </div>
    </div>

    <div class="row gy-4">
        @forelse($invoices as $invoice)
            <div class="col-md-4">
                <div class="invoice-card">
                    <div class="invoice-title">Invoice #{{ $invoice->invoice_number }}</div>
                    <div class="invoice-meta">
                        Period: {{ $invoice->billing_period_start->format('d M Y') }} -
                        {{ $invoice->billing_period_end->format('d M Y') }}
                    </div>
                    <div class="invoice-meta">
                        Hours: {{ $invoice->total_hours }} | Rate: ₹{{ $invoice->hourly_rate }}
                    </div>
                    <div class="invoice-meta">
                        Subtotal: ₹{{ $invoice->subtotal }} | Tax: ₹{{ $invoice->tax }}
                    </div>
                    <div class="invoice-meta fw-bold">
                        Total: ₹{{ $invoice->total_amount }}
                    </div>
                    <div class="invoice-status mt-2">
                        Status:
                        <span class="badge
                            @if($invoice->status === 'paid') bg-success
                            @elseif($invoice->status === 'overdue') bg-danger
                            @else bg-secondary @endif">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                    @if($invoice->notes)
                        <p class="mt-2 small text-muted">{{ $invoice->notes }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-receipt" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 text-muted">No Invoices Found</h4>
                <p class="text-muted">Invoices will appear here once generated.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
