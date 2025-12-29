@extends('layouts.app')

@section('title', 'Add Inventory Item')

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
                <h1>Add Inventory Item</h1>
                <p>Create a new inventory item</p>
            </div>
            <div class="profile-header-actions">
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-card">
                <div class="form-card-header">
                    <h5><i class="bi bi-box-seam me-2"></i>Inventory Information</h5>
                </div>
                <div class="form-card-body">
                    <form id="inventoryForm" method="POST" action="{{ route('admin.inventory.store') }}">
                        @csrf
                        <div id="formErrors" class="alert alert-danger d-none"></div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="bi bi-tag me-1"></i>Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter item name" required>

                            </div>

                            <div class="col-md-6">
                                <label for="category" class="form-label">
                                    <i class="bi bi-folder me-1"></i>Category
                                </label>
                                <input type="text" class="form-control" id="category" name="category" placeholder="e.g., Cleaning Supplies">

                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="bi bi-file-text me-1"></i>Description
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Item description..."></textarea>

                            </div>

                            <div class="col-md-6">
                                <label for="quantity" class="form-label">
                                    <i class="bi bi-123 me-1"></i>Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="quantity" name="quantity" placeholder="0" required min="0">

                            </div>

                            <div class="col-md-6">
                                <label for="unit" class="form-label">
                                    <i class="bi bi-rulers me-1"></i>Unit
                                </label>
                                <input type="text" class="form-control" id="unit" name="unit" placeholder="e.g., bottles, boxes, pieces">

                            </div>

                            <div class="col-md-6">
                                <label for="min_stock_level" class="form-label">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Min Stock Level
                                </label>
                                <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" placeholder="0" min="0">

                            </div>

                            <div class="col-md-6">
                                <label for="unit_cost" class="form-label">
                                    <i class="bi bi-currency-dollar me-1"></i>Unit Cost
                                </label>
                                <input type="number" step="0.01" class="form-control" id="unit_cost" name="unit_cost" placeholder="0.00" min="0">

                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Create Item
                            </button>
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/inventory.js'])
    <script>
        // Pass index route to JS
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('inventoryForm');
            if (form) {
                form.dataset.indexRoute = '{{ route("admin.inventory.index") }}';
            }
        });
    </script>
@endpush
