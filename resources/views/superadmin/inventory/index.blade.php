@extends('layouts.app')

@section('title', 'Inventory Management')

@push('styles')
    @vite(['resources/css/profile.css', 'resources/css/datatables.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Inventory Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <i class="bi bi-box-seam" style="font-size: 2.5rem;"></i>
            </div>
            <div class="profile-info flex-grow-1">
                <h1>Inventory Management</h1>
                <p>Manage cleaning inventory items</p>
            </div>
            @can('create inventory')
            <div class="profile-header-actions">
                <button type="button" class="btn btn-light btn-lg px-4 py-2 shadow-lg rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                    <i class="bi bi-plus-circle me-2"></i>Add Item
                </button>
            </div>
            @endcan
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-striped table-hover w-100']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Inventory Modal -->
<div class="modal fade" id="viewInventoryModal" tabindex="-1" aria-labelledby="viewInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewInventoryModalLabel">Inventory Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="inventoryDetails">
                <!-- Inventory details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="inventoryForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="item_id" name="id">
                    <div id="formErrors" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="min_stock_level" class="form-label">Min Stock Level</label>
                            <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" placeholder="e.g., bottles, boxes">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label for="unit_cost" class="form-label">Unit Cost</label>
                            <input type="number" step="0.01" class="form-control" id="unit_cost" name="unit_cost" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
<script>
    (function() {
        let isInitialized = false;
        
        function initInventoryScripts() {
            if (isInitialized) return;
            
            if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
                setTimeout(initInventoryScripts, 100);
                return;
            }

            const $ = window.jQuery;
            let isEdit = false;
            const form = document.getElementById('inventoryForm');
            if (!form || form.dataset.handlerAttached === 'true') return;
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const spinner = submitBtn.querySelector('.spinner-border');
            const btnText = submitBtn.querySelector('.btn-text');
            const formErrors = document.getElementById('formErrors');

            let isSubmitting = false;
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Prevent duplicate submissions
                if (isSubmitting) {
                    return;
                }
                isSubmitting = true;
                
                // Reset errors
                if (formErrors) {
                    formErrors.classList.add('d-none');
                    formErrors.innerHTML = '';
                }
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                // Show loading state
                submitBtn.disabled = true;
                spinner.classList.remove('d-none');
                btnText.textContent = isEdit ? 'Updating...' : 'Saving...';

                const formData = new FormData(form);
                const url = isEdit ? `/admin/inventory/${formData.get('id')}` : '{{ route("admin.inventory.store") }}`;
                const method = isEdit ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        $('#inventory-table').DataTable().ajax.reload();
                        
                        const modal = bootstrap?.Modal?.getInstance(document.getElementById('addInventoryModal'));
                        if (modal) {
                            modal.hide();
                        } else {
                            $('#addInventoryModal').modal('hide');
                        }
                        
                        if (typeof showToast !== 'undefined') {
                            showToast('success', data.message);
                        }
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            let errorHtml = '<ul class="mb-0">';
                            Object.keys(data.errors).forEach(field => {
                                const errorMessages = Array.isArray(data.errors[field]) ? data.errors[field] : [data.errors[field]];
                                errorMessages.forEach(error => {
                                    errorHtml += `<li>${error}</li>`;
                                });
                                
                                // Highlight field
                                const fieldElement = form.querySelector(`[name="${field}"]`);
                                if (fieldElement) {
                                    fieldElement.classList.add('is-invalid');
                                    const feedback = fieldElement.parentElement.querySelector('.invalid-feedback');
                                    if (feedback) {
                                        feedback.textContent = errorMessages[0];
                                    }
                                }
                            });
                            errorHtml += '</ul>';
                            formErrors.innerHTML = errorHtml;
                            formErrors.classList.remove('d-none');
                        }
                        
                        showToast?.('error', data.message || 'Please fix the errors and try again') || 
                        toastr?.error(data.message || 'Please fix the errors and try again') || 
                        alert(data.message || 'Please fix the errors and try again');
                    }
                } catch (error) {
                    showToast?.('error', 'Failed to save inventory item: ' + error.message) ||
                    toastr?.error('Failed to save inventory item: ' + error.message) ||
                    alert('Failed to save inventory item: ' + error.message);
                } finally {
                    // Reset loading state
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    spinner.classList.add('d-none');
                    btnText.textContent = 'Save';
                }
            });
            
            form.dataset.handlerAttached = 'true';

            // Helper function to format numbers
            function formatNumber(num) {
                if (num === null || num === undefined) return '0';
                return parseFloat(num).toLocaleString('en-US');
            }

            // jQuery is loaded globally from npm via layout
            $(document).ready(function() {
                $(document).on('click', '.view-inventory', function() {
                    try {
                        const itemData = $(this).attr('data-item');
                        if (!itemData) {
                            showToast?.('error', 'Unable to load inventory details');
                            return;
                        }
                        
                        const item = JSON.parse(itemData);
                        const statusColors = {
                            'available': 'success',
                            'assigned': 'info',
                            'used': 'warning',
                            'returned': 'secondary'
                        };
                        const statusColor = statusColors[item.status] || 'secondary';
                        const quantity = item.quantity ? formatNumber(item.quantity) : '0';
                        const minStock = item.min_stock_level ? formatNumber(item.min_stock_level) : 'Not set';
                        const isLowStock = item.quantity !== null && item.min_stock_level !== null && 
                                          parseFloat(item.quantity) <= parseFloat(item.min_stock_level);
                        
                        const html = `
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Name:</strong>
                                    <p class="mb-0">${item.name || 'N/A'}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Category:</strong>
                                    <p class="mb-0">${item.category || 'N/A'}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <strong>Description:</strong>
                                    <p class="mb-0">${item.description || 'No description'}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Quantity:</strong>
                                    <p class="mb-0">${quantity} ${item.unit || 'units'}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Min Stock Level:</strong>
                                    <p class="mb-0">${minStock}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Unit:</strong>
                                    <p class="mb-0">${item.unit || 'N/A'}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Unit Cost:</strong>
                                    <p class="mb-0">${item.unit_cost ? '$' + parseFloat(item.unit_cost).toFixed(2) : 'N/A'}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Status:</strong>
                                    <p class="mb-0"><span class="badge bg-${statusColor}">${item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : 'N/A'}</span></p>
                                </div>
                                ${isLowStock ? `
                                <div class="col-12 mb-3">
                                    <div class="alert alert-warning mb-0">
                                        <i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alert: Quantity is at or below minimum stock level
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        `;
                        
                        $('#inventoryDetails').html(html);
                        new bootstrap.Modal(document.getElementById('viewInventoryModal')).show();
                    } catch (error) {
                        showToast?.('error', 'Failed to load inventory details: ' + error.message) ||
                        alert('Failed to load inventory details: ' + error.message);
                    }
                });

                $(document).on('click', '.edit-inventory', function() {
                    try {
                        const itemData = $(this).attr('data-item');
                        if (!itemData) {
                            showToast?.('error', 'Unable to load inventory item for editing');
                            return;
                        }
                        
                        const item = JSON.parse(itemData);
                        document.getElementById('item_id').value = item.id || '';
                        document.getElementById('name').value = item.name || '';
                        document.getElementById('description').value = item.description || '';
                        document.getElementById('category').value = item.category || '';
                        document.getElementById('quantity').value = item.quantity || 0;
                        document.getElementById('min_stock_level').value = item.min_stock_level || '';
                        document.getElementById('unit').value = item.unit || '';
                        document.getElementById('unit_cost').value = item.unit_cost || '';
                        document.querySelector('#addInventoryModal .modal-title').textContent = 'Edit Inventory Item';
                        
                        if (formErrors) {
                            formErrors.classList.add('d-none');
                            formErrors.innerHTML = '';
                        }
                        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
                        
                        isEdit = true;
                        new bootstrap.Modal(document.getElementById('addInventoryModal')).show();
                    } catch (error) {
                        showToast?.('error', 'Failed to load inventory item: ' + error.message) ||
                        alert('Failed to load inventory item: ' + error.message);
                    }
                });

                $(document).on('click', '.delete-inventory', function() {
                    const itemId = $(this).data('id');
                    if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                        fetch(`/admin/inventory/${itemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                $('#inventory-table').DataTable().ajax.reload();
                                showToast?.('success', data.message) || toastr?.success(data.message) || alert(data.message);
                            } else {
                                showToast?.('error', data.message || 'Failed to delete item') ||
                                toastr?.error(data.message || 'Failed to delete item') ||
                                alert(data.message || 'Failed to delete item');
                            }
                        })
                        .catch(error => {
                            showToast?.('error', 'Error deleting item: ' + error.message) ||
                            toastr?.error('Error deleting item: ' + error.message) ||
                            alert('Error deleting item: ' + error.message);
                        });
                    }
                });
            });

            document.getElementById('addInventoryModal')?.addEventListener('hidden.bs.modal', function() {
                form.reset();
                document.getElementById('item_id').value = '';
                document.querySelector('#addInventoryModal .modal-title').textContent = 'Add Inventory Item';
                if (formErrors) {
                    formErrors.classList.add('d-none');
                    formErrors.innerHTML = '';
                }
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
                isEdit = false;
            });
            
            isInitialized = true;
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initInventoryScripts);
        } else {
            initInventoryScripts();
        }
    })();
</script>
@endpush
@endsection

