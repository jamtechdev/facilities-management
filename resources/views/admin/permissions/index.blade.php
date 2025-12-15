@extends('layouts.app')

@section('title', 'Permissions Management')

@push('styles')
<style>
    .permission-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s;
    }
    .permission-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .permission-group-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Permissions Management</h1>
                    <p class="text-muted">Create and manage system permissions</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                        <i class="bi bi-plus-circle me-2"></i>Create Permission
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-people me-2"></i>Manage Roles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-container"></div>

    <div class="row g-4">
        @foreach($permissions as $group => $groupPermissions)
        <div class="col-12">
            <div class="card permission-card border-0 shadow-sm">
                <div class="permission-group-header">
                    <h5 class="mb-0 text-capitalize">
                        <i class="bi bi-folder-fill me-2"></i>{{ ucfirst($group) }} Permissions
                        <span class="badge bg-light text-dark ms-2">{{ $groupPermissions->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($groupPermissions as $permission)
                        <div class="col-md-4 col-lg-3">
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                <span class="small">{{ $permission->name }}</span>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary btn-sm edit-permission" 
                                            data-id="{{ $permission->id }}" 
                                            data-name="{{ $permission->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-permission" 
                                            data-id="{{ $permission->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Add/Edit Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalTitle">Create New Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="permissionForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="permission_id" name="id">
                    <div class="mb-3">
                        <label for="permission_name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="permission_name" name="name" required 
                               placeholder="e.g., view reports, create invoices">
                        <small class="text-muted">Use lowercase with spaces (e.g., "view leads", "create clients")</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let isEditPermission = false;
    let currentPermissionId = null;

    // Edit Permission
    document.querySelectorAll('.edit-permission').forEach(btn => {
        btn.addEventListener('click', function() {
            currentPermissionId = this.dataset.id;
            isEditPermission = true;
            
            document.getElementById('permission_id').value = this.dataset.id;
            document.getElementById('permission_name').value = this.dataset.name;
            document.getElementById('permissionModalTitle').textContent = 'Edit Permission';
            
            new bootstrap.Modal(document.getElementById('addPermissionModal')).show();
        });
    });

    // Delete Permission
    document.querySelectorAll('.delete-permission').forEach(btn => {
        btn.addEventListener('click', function() {
            const permissionId = this.dataset.id;
            
            if (confirm('Are you sure you want to delete this permission? This action cannot be undone.')) {
                fetch(`/admin/permissions/${permissionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        });
    });

    // Form Submission
    document.getElementById('permissionForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const data = {
            name: formData.get('name'),
            _token: '{{ csrf_token() }}'
        };

        const url = isEditPermission ? `/admin/permissions/${currentPermissionId}` : '{{ route("admin.permissions.store") }}';
        const method = isEditPermission ? 'PUT' : 'POST';

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                location.reload();
            } else {
                showAlert('danger', result.message);
            }
        } catch (error) {
            showAlert('danger', 'Failed to save permission: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Reset modal on close
    document.getElementById('addPermissionModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('permissionForm').reset();
        document.getElementById('permission_id').value = '';
        document.getElementById('permissionModalTitle').textContent = 'Create New Permission';
        isEditPermission = false;
        currentPermissionId = null;
    });

    function showAlert(type, message) {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
</script>
@endpush
@endsection

