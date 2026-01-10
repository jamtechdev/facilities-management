@extends('layouts.app')

@section('title', 'Inventory Item Details')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Inventory Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <i class="bi bi-box-seam icon-2-5rem"></i>
            </div>
            <div class="profile-info flex-grow-1">
                <h1>{{ $inventory->name }}</h1>
                <p>{{ $inventory->category ?? 'No category' }}</p>
            </div>
            <div class="profile-header-actions">
                @can('edit inventory')
                <a href="{{ \App\Helpers\RouteHelper::url('inventory.edit', $inventory) }}" class="btn btn-light me-2">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                @endcan
                <a href="{{ \App\Helpers\RouteHelper::url('inventory.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="bi bi-info-circle me-2"></i>Item Information
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Name</label>
                                <div class="fs-6">{{ $inventory->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Category</label>
                                <div class="fs-6">{{ $inventory->category ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Description</label>
                                <div class="fs-6">{{ $inventory->description ?? 'No description provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Quantity</label>
                                <div class="fs-6">{{ number_format($inventory->quantity) }} {{ $inventory->unit ?? 'units' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Minimum Stock Level</label>
                                <div class="fs-6">{{ $inventory->min_stock_level ? number_format($inventory->min_stock_level) : 'Not set' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Unit</label>
                                <div class="fs-6">{{ $inventory->unit ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Unit Cost</label>
                                <div class="fs-6">{{ $inventory->unit_cost ? '$' . number_format($inventory->unit_cost, 2) : 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Status</label>
                                <div class="fs-6">
                                    @php
                                        $statusColors = [
                                            'available' => 'success',
                                            'assigned' => 'info',
                                            'used' => 'warning',
                                            'returned' => 'secondary'
                                        ];
                                        $statusColor = $statusColors[$inventory->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst($inventory->status) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Stock Status</label>
                                <div class="fs-6">
                                    @if($inventory->isLowStock())
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($inventory->assignedTo)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Assigned To</label>
                                <div class="fs-6">
                                    @if($inventory->assigned_to_type === 'App\Models\Staff')
                                        {{ $inventory->assignedTo->name }}
                                    @else
                                        {{ $inventory->assignedTo->company_name ?? 'N/A' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Assignment Type</label>
                                <div class="fs-6">{{ $inventory->assigned_to_type === 'App\Models\Staff' ? 'Staff' : 'Client' }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Created</label>
                                <div class="fs-6">{{ $inventory->created_at ? $inventory->created_at->format('M d, Y h:i A') : 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted small">Last Updated</label>
                                <div class="fs-6">{{ $inventory->updated_at ? $inventory->updated_at->format('M d, Y h:i A') : 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="bi bi-calculator me-2"></i>Stock Information
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Current Quantity</label>
                        <div class="fs-4 fw-bold">{{ number_format($inventory->quantity) }} {{ $inventory->unit ?? 'units' }}</div>
                    </div>

                    @if($inventory->min_stock_level)
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Minimum Stock Level</label>
                        <div class="fs-6">{{ number_format($inventory->min_stock_level) }} {{ $inventory->unit ?? 'units' }}</div>
                    </div>

                    @php
                        $remaining = $inventory->quantity - $inventory->min_stock_level;
                        $percentage = $inventory->min_stock_level > 0 ? ($inventory->quantity / $inventory->min_stock_level) * 100 : 100;
                    @endphp
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Stock Above Minimum</label>
                        <div class="fs-6 {{ $remaining < 0 ? 'text-danger' : 'text-success' }}">
                            {{ $remaining >= 0 ? '+' : '' }}{{ number_format($remaining) }} {{ $inventory->unit ?? 'units' }}
                        </div>
                    </div>
                    @endif

                    @if($inventory->unit_cost)
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Total Value</label>
                        <div class="fs-4 fw-bold text-success">${{ number_format($inventory->quantity * $inventory->unit_cost, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
