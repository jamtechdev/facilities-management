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

    <div id="alert-container"></div>

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
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category">
                        </div>
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="min_stock_level" class="form-label">Min Stock Level</label>
                            <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" placeholder="e.g., bottles, boxes">
                        </div>
                        <div class="col-12">
                            <label for="unit_cost" class="form-label">Unit Cost</label>
                            <input type="number" step="0.01" class="form-control" id="unit_cost" name="unit_cost" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
<script>
    let isEdit = false;

    document.getElementById('inventoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = isEdit ? `/admin/inventory/${formData.get('id')}` : '{{ route("admin.inventory.store") }}';
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
                document.getElementById('addInventoryModal').querySelector('[data-bs-dismiss="modal"]').click();
                if (typeof showToast !== 'undefined') {
                    showToast('success', data.message);
                }
            } else {
                if (typeof showToast !== 'undefined') {
                    showToast('error', data.message);
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(data.message);
                } else {
                    alert(data.message);
                }
            }
        } catch (error) {
            if (typeof showToast !== 'undefined') {
                showToast('error', 'Failed to save inventory item: ' + error.message);
            } else if (typeof toastr !== 'undefined') {
                toastr.error('Failed to save inventory item: ' + error.message);
            } else {
                alert('Failed to save inventory item: ' + error.message);
            }
        }
    });

    // Handle edit button clicks from DataTable
    $(document).on('click', '.edit-inventory', function() {
        const item = JSON.parse($(this).data('item'));
        document.getElementById('item_id').value = item.id;
        document.getElementById('name').value = item.name;
        document.getElementById('description').value = item.description || '';
        document.getElementById('category').value = item.category || '';
        document.getElementById('quantity').value = item.quantity;
        document.getElementById('min_stock_level').value = item.min_stock_level || '';
        document.getElementById('unit').value = item.unit || '';
        document.getElementById('unit_cost').value = item.unit_cost || '';
        document.querySelector('#addInventoryModal .modal-title').textContent = 'Edit Inventory Item';
        isEdit = true;
        new bootstrap.Modal(document.getElementById('addInventoryModal')).show();
    });

    // Handle delete button clicks from DataTable
    $(document).on('click', '.delete-inventory', function() {
        const itemId = $(this).data('id');
        if (confirm('Are you sure you want to delete this item?')) {
            fetch(`/admin/inventory/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#inventory-table').DataTable().ajax.reload();
                    if (typeof showToast !== 'undefined') {
                        showToast('success', data.message);
                    }
                }
            });
        }
    });

    document.getElementById('addInventoryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('inventoryForm').reset();
        document.getElementById('item_id').value = '';
        document.querySelector('#addInventoryModal .modal-title').textContent = 'Add Inventory Item';
        isEdit = false;
    });
</script>
@endpush
@endsection

