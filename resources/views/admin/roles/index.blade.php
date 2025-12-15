@extends('layouts.app')

@section('title', 'Roles & Permissions')

@push('styles')
<style>
    .permission-group {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .permission-item {
        display: flex;
        align-items: center;
        padding: 8px;
        border-radius: 4px;
        transition: background 0.2s;
    }
    .permission-item:hover {
        background: #e9ecef;
    }
    .role-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s;
    }
    .role-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .permission-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    .role-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px 12px 0 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Roles & Permissions</h1>
                    <p class="text-muted">Manage user roles and their permissions</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        <i class="bi bi-plus-circle me-2"></i>Create Role
                    </button>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-shield-check me-2"></i>Manage Permissions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-container"></div>

    <div class="row g-4">
        @foreach($roles as $role)
        <div class="col-lg-6">
            <div class="card role-card border-0 shadow-sm h-100">
                <div class="role-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-1 text-white">{{ $role->name }}</h4>
                            <p class="mb-0 text-white-50 small">
                                <i class="bi bi-people me-1"></i>{{ $role->users_count }} user(s) assigned
                            </p>
                        </div>
                        @if($role->name === 'SuperAdmin')
                            <span class="badge bg-warning">
                                <i class="bi bi-shield-fill me-1"></i>Protected
                            </span>
                        @elseif($role->name === 'Admin')
                            <span class="badge bg-info">
                                <i class="bi bi-shield-check me-1"></i>Protected
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Permissions ({{ $role->permissions->count() }})</h6>
                        @if($role->permissions->count() > 0)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($role->permissions->take(10) as $permission)
                                    <span class="badge bg-primary permission-badge">{{ $permission->name }}</span>
                                @endforeach
                                @if($role->permissions->count() > 10)
                                    <span class="badge bg-secondary permission-badge">+{{ $role->permissions->count() - 10 }} more</span>
                                @endif
                            </div>
                        @else
                            <p class="text-muted small mb-0">No permissions assigned</p>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-role" data-role='@json($role)'>
                            <i class="bi bi-pencil me-1"></i>Edit
                        </button>
                        @if(!in_array($role->name, ['SuperAdmin', 'Admin']))
                            <button type="button" class="btn btn-sm btn-outline-danger delete-role" data-id="{{ $role->id }}" data-name="{{ $role->name }}">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Add/Edit Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="role_id" name="id">
                    <div class="mb-3">
                        <label for="role_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="role_name" name="name" required placeholder="e.g., Manager, Supervisor">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Permissions</label>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            @foreach($permissions as $group => $groupPermissions)
                                <div class="permission-group mb-3">
                                    <h6 class="mb-2 text-capitalize">
                                        <i class="bi bi-folder me-1"></i>{{ ucfirst($group) }}
                                    </h6>
                                    <div class="row g-2">
                                        @foreach($groupPermissions as $permission)
                                            <div class="col-md-6">
                                                <div class="permission-item">
                                                    <input type="checkbox" 
                                                           class="form-check-input me-2 permission-checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}" 
                                                           id="perm_{{ $permission->id }}">
                                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                <i class="bi bi-check-all me-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                                <i class="bi bi-x-square me-1"></i>Deselect All
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let isEdit = false;
    let currentRoleId = null;

    // Select/Deselect All
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
    });

    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    });

    // Edit Role
    document.querySelectorAll('.edit-role').forEach(btn => {
        btn.addEventListener('click', function() {
            const role = JSON.parse(this.dataset.role);
            currentRoleId = role.id;
            isEdit = true;
            
            document.getElementById('role_id').value = role.id;
            document.getElementById('role_name').value = role.name;
            document.getElementById('modalTitle').textContent = 'Edit Role: ' + role.name;
            
            // Uncheck all first
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            
            // Check assigned permissions
            role.permissions.forEach(perm => {
                const checkbox = document.getElementById('perm_' + perm.id);
                if (checkbox) checkbox.checked = true;
            });
            
            new bootstrap.Modal(document.getElementById('addRoleModal')).show();
        });
    });

    // Delete Role
    document.querySelectorAll('.delete-role').forEach(btn => {
        btn.addEventListener('click', function() {
            const roleId = this.dataset.id;
            const roleName = this.dataset.name;
            
            if (confirm(`Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`)) {
                fetch(`/admin/roles/${roleId}`, {
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
    document.getElementById('roleForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const permissions = Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => cb.value);
        
        const data = {
            name: formData.get('name'),
            permissions: permissions,
            _token: '{{ csrf_token() }}'
        };

        const url = isEdit ? `/admin/roles/${currentRoleId}` : '{{ route("admin.roles.store") }}';
        const method = isEdit ? 'PUT' : 'POST';

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
            showAlert('danger', 'Failed to save role: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Reset modal on close
    document.getElementById('addRoleModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('roleForm').reset();
        document.getElementById('role_id').value = '';
        document.getElementById('modalTitle').textContent = 'Create New Role';
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        isEdit = false;
        currentRoleId = null;
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

