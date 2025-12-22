@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Inventory Management</h1>
                    <p class="text-muted">Manage cleaning inventory items</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                    <i class="bi bi-plus-circle me-2"></i>Add Item
                </button>
            </div>
        </div>
    </div>

    <div id="alert-container"></div>

    <div class="row">
        @foreach($inventory as $item)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ $item->name }}</h5>
                        <span class="badge bg-{{ $item->isLowStock() ? 'danger' : 'success' }}">
                            {{ $item->status }}
                        </span>
                    </div>
                    <p class="text-muted small mb-2">{{ $item->description }}</p>
                    <div class="mb-2">
                        <strong>Quantity:</strong> {{ $item->quantity }} {{ $item->unit ?? 'units' }}
                        @if($item->isLowStock())
                            <span class="badge bg-warning">Low Stock</span>
                        @endif
                    </div>
                    @if($item->category)
                    <div class="mb-2">
                        <strong>Category:</strong> {{ $item->category }}
                    </div>
                    @endif
                    @if($item->unit_cost)
                    <div class="mb-2">
                        <strong>Unit Cost:</strong> ${{ number_format($item->unit_cost, 2) }}
                    </div>
                    @endif
                    @if($item->assignedTo)
                    <div class="mb-2">
                        <strong>Assigned To:</strong>
                        @if($item->assigned_to_type === 'App\Models\Staff')
                            Staff: {{ $item->assignedTo->name }}
                        @else
                            Client: {{ $item->assignedTo->company_name }}
                        @endif
                    </div>
                    @endif
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary edit-item" data-item='@json($item)'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger delete-item" data-id="{{ $item->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
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
                location.reload();
            } else {
                alert(data.message);
            }
        } catch (error) {
            alert('Failed to save inventory item: ' + error.message);
        }
    });

    document.querySelectorAll('.edit-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = JSON.parse(this.dataset.item);
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
    });

    document.querySelectorAll('.delete-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this item?')) {
                fetch(`/admin/inventory/${this.dataset.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => location.reload());
            }
        });
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

