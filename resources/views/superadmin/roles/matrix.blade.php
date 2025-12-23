@extends('layouts.app')

@section('title', 'Roles & Permissions Matrix')

@push('styles')
<style>
    .permission-matrix-wrapper {
        padding: 8px;
        background: transparent;
        min-height: calc(100vh - 120px);
    }
    
    .matrix-table-container {
        overflow-x: auto;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }
    
    .matrix-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
    }
    
    .matrix-table thead {
        position: sticky;
        top: 0;
        z-index: 20;
    }
    
    .matrix-table thead tr {
        background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
    }
    
    .matrix-table th {
        padding: 12px 14px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: white;
        border-right: 1px solid rgba(255,255,255,0.12);
        white-space: nowrap;
    }
    
    .matrix-table th:first-child {
        background: linear-gradient(135deg, #6ba85a 0%, #5a8a4d 100%);
        min-width: 220px;
        position: sticky;
        left: 0;
        z-index: 15;
        box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    }
    
    .matrix-table th:nth-child(2) {
        background: linear-gradient(135deg, #6ba85a 0%, #5a8a4d 100%);
        min-width: 250px;
        position: sticky;
        left: 220px;
        z-index: 15;
        box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    }
    
    .matrix-table th.role-column {
        text-align: center;
        min-width: 140px;
        background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
        font-weight: 700;
        font-size: 13px;
    }
    
    .matrix-table th.role-column.your-role {
        background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
        box-shadow: 0 0 15px rgba(132, 195, 115, 0.6);
        animation: pulse-glow 2s ease-in-out infinite;
    }
    
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 15px rgba(132, 195, 115, 0.6); }
        50% { box-shadow: 0 0 25px rgba(132, 195, 115, 0.8); }
    }
    
    .matrix-table th.role-column.superadmin-role {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }
    
    .matrix-table th.role-column.admin-role {
        background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
    }
    
    .matrix-table th.role-column.staff-role {
        background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
    }
    
    .matrix-table th.role-column.client-role {
        background: linear-gradient(135deg, #a8d99a 0%, #84c373 100%);
    }
    
    .matrix-table th.role-column.lead-role {
        background: linear-gradient(135deg, #a3a3a3 0%, #737373 100%);
    }
    
    .role-badge {
        display: block;
        margin-top: 4px;
        padding: 3px 10px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(255,255,255,0.3);
        backdrop-filter: blur(8px);
    }
    
    .matrix-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.15s ease;
        background-color: #ffffff;
    }
    
    .matrix-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .matrix-table tbody tr:nth-child(even) {
        background-color: #ffffff;
    }
    
    .matrix-table tbody tr:nth-child(even):hover {
        background-color: #f8f9fa;
    }
    
    .matrix-table td {
        padding: 12px 14px;
        border-right: 1px solid #e5e7eb;
        vertical-align: middle;
        color: #1e293b;
        font-size: 14px;
        background-color: #ffffff;
    }
    
    .matrix-table td:first-child {
        background: #ffffff;
        font-weight: 600;
        color: #1e293b;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        position: sticky;
        left: 0;
        z-index: 10;
        box-shadow: 2px 0 4px rgba(0,0,0,0.04);
    }
    
    .matrix-table td:nth-child(2) {
        background: #ffffff;
        color: #1e293b;
        font-size: 14px;
        font-weight: 500;
        position: sticky;
        left: 220px;
        z-index: 10;
        box-shadow: 2px 0 4px rgba(0,0,0,0.04);
    }
    
    .permission-checkbox-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .permission-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #84c373;
        transition: all 0.2s ease;
    }
    
    .permission-checkbox:hover:not(:disabled) {
        transform: scale(1.15);
        accent-color: #6ba85a;
    }
    
    .permission-checkbox:disabled {
        cursor: not-allowed;
        opacity: 0.35;
    }
    
    .permission-checkbox:checked {
        accent-color: #84c373;
    }
    
    .loading-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: none;
    }
    
    .permission-checkbox.loading + .loading-overlay {
        display: block;
    }
    
    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid #d1fae5;
        border-top-color: #84c373;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .toast-notification {
        position: fixed;
        top: 16px;
        right: 16px;
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        z-index: 9999;
        display: none;
        min-width: 280px;
        backdrop-filter: blur(10px);
        animation: slideInRight 0.3s ease-out;
    }
    
    .toast-notification.show {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .toast-notification.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .toast-notification.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .toast-notification i {
        font-size: 18px;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 40px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    
    .group-header-row {
        background: linear-gradient(135deg, rgba(132, 195, 115, 0.1) 0%, rgba(107, 168, 90, 0.1) 100%) !important;
    }
    
    .group-header-cell {
        background: linear-gradient(135deg, rgba(132, 195, 115, 0.15) 0%, rgba(107, 168, 90, 0.15) 100%) !important;
        padding: 12px 16px !important;
        border-bottom: 2px solid rgba(132, 195, 115, 0.3) !important;
        position: sticky;
        left: 0;
        z-index: 12;
    }
    
    .group-header-content {
        display: flex;
        align-items: center;
        font-size: 13px;
        color: #5a8a4d;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    
    .group-header-content i {
        font-size: 18px;
        color: #84c373;
        margin-right: 10px;
        display: inline-block;
        width: auto;
        height: auto;
    }
    
    .group-count {
        margin-left: auto;
        font-size: 12px;
        font-weight: 600;
        color: #6ba85a;
        text-transform: none;
        letter-spacing: 0;
    }
    
    .matrix-table tbody tr.group-header-row:hover {
        background: linear-gradient(135deg, rgba(132, 195, 115, 0.2) 0%, rgba(107, 168, 90, 0.2) 100%) !important;
    }
</style>
@endpush

@section('content')
<div class="permission-matrix-wrapper">
    <!-- Header with Create Permission Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Roles & Permissions Matrix</h2>
        @if(auth()->user()->hasRole('SuperAdmin'))
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
            <i class="bi bi-plus-circle me-2"></i>Create Permission
        </button>
        @endif
    </div>
    
    @if(count($permissionGroups) > 0 && $roles->count() > 0)
        <div class="matrix-table-container">
            <table class="matrix-table">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Function</th>
                            @foreach($roles as $role)
                                @php
                                    $roleClass = strtolower($role->name) . '-role';
                                    if ($currentUserRole && $currentUserRole->id === $role->id) {
                                        $roleClass .= ' your-role';
                                    }
                                @endphp
                                <th class="role-column {{ $roleClass }}">
                                    {{ strtoupper($role->name) }}
                                    @if($currentUserRole && $currentUserRole->id === $role->id)
                                        <span class="role-badge">YOU</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissionGroups as $groupName => $permissions)
                            <!-- Group Header Row -->
                            <tr class="group-header-row">
                                <td colspan="{{ 2 + $roles->count() }}" class="group-header-cell">
                                    <div class="group-header-content">
                                        <i class="bi bi-folder-fill"></i>
                                        <strong>{{ strtoupper($groupName) }}</strong>
                                        <span class="group-count">{{ count($permissions) }} items</span>
                                    </div>
                                </td>
                            </tr>
                            <!-- Permissions in this group -->
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>
                                        {{ $permission->name }}
                                        @if(auth()->user()->hasRole('SuperAdmin'))
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2 delete-permission" 
                                                data-id="{{ $permission->id }}" 
                                                data-name="{{ $permission->name }}"
                                                title="Delete Permission">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </td>
                                    <td>{{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}</td>
                                    @foreach($roles as $role)
                                        <td>
                                            @php
                                                $hasPermission = $role->hasPermissionTo($permission);
                                                $isSuperAdmin = $role->name === 'SuperAdmin';
                                                $isAdmin = $role->name === 'Admin';
                                                $user = auth()->user();
                                                $canEdit = !$isSuperAdmin && ($isAdmin ? ($user && $user->hasRole('SuperAdmin')) : true);
                                            @endphp
                                            <div class="permission-checkbox-wrapper">
                                                <input 
                                                    type="checkbox" 
                                                    class="permission-checkbox {{ !$canEdit ? 'disabled' : '' }}" 
                                                    data-role-id="{{ $role->id }}" 
                                                    data-permission-id="{{ $permission->id }}"
                                                    data-role-name="{{ $role->name }}"
                                                    {{ $hasPermission ? 'checked' : '' }}
                                                    {{ !$canEdit ? 'disabled' : '' }}
                                                >
                                                <div class="loading-overlay">
                                                    <div class="spinner"></div>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>No Data Available</h4>
            <p>No roles or permissions found. Please run the seeder to create default data.</p>
        </div>
    @endif
</div>

<!-- Toast Notification -->
<div class="toast-notification" id="toastNotification">
    <i class="bi" id="toastIcon"></i>
    <span id="toastMessage"></span>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.permission-checkbox:not(:disabled)');
    const toast = document.getElementById('toastNotification');
    const toastIcon = document.getElementById('toastIcon');
    const toastMessage = document.getElementById('toastMessage');
    
    // Check if toast elements exist
    if (!toast || !toastIcon || !toastMessage) {
        console.error('Toast notification elements not found');
        return;
    }
    
    function showToast(message, type = 'success') {
        // Use toastr if available, otherwise fallback to custom toast
        if (typeof window.showToast !== 'undefined' && window.showToast !== showToast) {
            window.showToast(type, message);
            return;
        }
        
        if (typeof toastr !== 'undefined') {
            const toastType = type === 'error' ? 'error' : type;
            toastr[toastType](message);
            return;
        }
        
        // Fallback to custom toast
        if (!toastMessage || !toast) return;
        
        toastMessage.textContent = message;
        toast.className = `toast-notification ${type} show`;
        
        if (type === 'success') {
            toastIcon.className = 'bi bi-check-circle-fill';
        } else {
            toastIcon.className = 'bi bi-x-circle-fill';
        }
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const roleId = this.dataset.roleId;
            const permissionId = this.dataset.permissionId;
            const roleName = this.dataset.roleName;
            const grant = this.checked;
            const originalState = !grant;
            
            // Validate required data attributes
            if (!roleId || !permissionId || !roleName) {
                this.checked = originalState;
                showToast('Invalid permission data. Please refresh the page.', 'error');
                return;
            }
            
            // Show loading state
            this.disabled = true;
            this.classList.add('loading');
            
            // Make AJAX request
            fetch('{{ \App\Helpers\RouteHelper::url("roles.permissions.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    role_id: roleId,
                    permission_id: permissionId,
                    grant: grant
                })
            })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Server error occurred');
                    }).catch(() => {
                        throw new Error(`Server error: ${response.status} ${response.statusText}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                this.disabled = false;
                this.classList.remove('loading');
                
                if (data && data.success) {
                    const action = grant ? 'granted' : 'revoked';
                    showToast(`Permission ${action} for ${roleName} role`, 'success');
                } else {
                    // Revert checkbox state
                    this.checked = originalState;
                    showToast(data?.message || 'Failed to update permission', 'error');
                }
            })
            .catch(error => {
                this.disabled = false;
                this.classList.remove('loading');
                this.checked = originalState;
                const errorMessage = error.message || 'An error occurred. Please try again.';
                showToast(errorMessage, 'error');
                console.error('Permission update error:', error);
            });
        });
    });
});

// Create Permission Modal Handler
document.getElementById('createPermissionForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    
    try {
        const response = await fetch('{{ route("superadmin.permissions.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Permission created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createPermissionModal')).hide();
            this.reset();
            // Reload page to show new permission
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Failed to create permission', 'error');
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errorMsg = data.errors[key][0];
                    showToast(errorMsg, 'error');
                });
            }
        }
    } catch (error) {
        showToast('An error occurred. Please try again.', 'error');
        console.error('Permission creation error:', error);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Delete Permission Handler
document.querySelectorAll('.delete-permission').forEach(btn => {
    btn.addEventListener('click', async function() {
        const permissionId = this.dataset.id;
        const permissionName = this.dataset.name;
        
        if (!confirm(`Are you sure you want to delete permission "${permissionName}"?`)) {
            return;
        }
        
        try {
            const response = await fetch(`/superadmin/permissions/${permissionId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Permission deleted successfully', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.message || 'Failed to delete permission', 'error');
            }
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
            console.error('Permission deletion error:', error);
        }
    });
});
</script>

<!-- Create Permission Modal -->
@if(auth()->user()->hasRole('SuperAdmin'))
<div class="modal fade" id="createPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createPermissionForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="permission_name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="permission_name" name="name" required 
                               placeholder="e.g., view reports, edit reports, delete reports">
                        <small class="form-text text-muted">Use lowercase with spaces or underscores (e.g., "view reports" or "view_reports")</small>
                    </div>
                    <div class="mb-3">
                        <label for="permission_group" class="form-label">Group (Optional)</label>
                        <input type="text" class="form-control" id="permission_group" name="group" 
                               placeholder="e.g., Reports, Users, Settings">
                        <small class="form-text text-muted">Group name for organizing permissions</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Create Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endpush
