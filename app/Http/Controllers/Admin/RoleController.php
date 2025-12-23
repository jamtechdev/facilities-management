<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display permission matrix (single page for roles and permissions)
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $allPermissions = Permission::orderBy('name')->get();
        
        // Group permissions by prefix (first word before space/underscore)
        $permissionGroups = [];
        foreach ($allPermissions as $permission) {
            // Extract prefix (first word)
            $parts = preg_split('/[\s_-]+/', $permission->name);
            $prefix = ucfirst($parts[0] ?? 'other');
            
            // Group by prefix
            if (!isset($permissionGroups[$prefix])) {
                $permissionGroups[$prefix] = [];
            }
            $permissionGroups[$prefix][] = $permission;
        }
        
        // Sort groups alphabetically
        ksort($permissionGroups);
        
        // Get current user's role for highlighting
        $currentUser = auth()->user();
        $currentUserRole = $currentUser ? $currentUser->roles->first() : null;
        
        return view('superadmin.roles.matrix', compact('roles', 'permissionGroups', 'currentUserRole'));
    }
    
    /**
     * Update role permission (toggle permission for a role)
     */
    public function updatePermission(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
            'grant' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::findOrFail($request->role_id);
            $permission = Permission::findOrFail($request->permission_id);
            
            // Prevent editing SuperAdmin role permissions
            if ($role->name === 'SuperAdmin') {
                return response()->json([
                    'success' => false,
                    'message' => 'SuperAdmin role permissions cannot be modified.'
                ], 403);
            }

            // Check permission to edit roles
            $currentUser = auth()->user();
            if (!$currentUser->can('edit roles')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to modify role permissions.'
                ], 403);
            }

            if ($request->grant) {
                $role->givePermissionTo($permission);
            } else {
                $role->revokePermissionTo($permission);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully.',
                'data' => [
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                    'has_permission' => $role->hasPermissionTo($permission)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function() use ($request) {
                $role = Role::create(['name' => $request->name]);
                
                if ($request->has('permissions')) {
                    $permissions = Permission::whereIn('id', $request->permissions)->get();
                    $role->syncPermissions($permissions);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Role created successfully.',
                    'data' => $role->load('permissions')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $user = auth()->user();
        
        // Prevent editing SuperAdmin role - no one can modify it
        if ($role->name === 'SuperAdmin') {
            return response()->json([
                'success' => false,
                'message' => 'SuperAdmin role cannot be modified.'
            ], 403);
        }

        // Check permission to edit roles
        if (!$user->can('edit roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to modify roles.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function() use ($request, $role) {
                $role->update(['name' => $request->name]);
                
                if ($request->has('permissions')) {
                    $permissions = Permission::whereIn('id', $request->permissions)->get();
                    $role->syncPermissions($permissions);
                } else {
                    $role->syncPermissions([]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Role updated successfully.',
                    'data' => $role->load('permissions')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role): JsonResponse
    {
        // Prevent deleting Admin and SuperAdmin roles
        if (in_array($role->name, ['Admin', 'SuperAdmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'This role cannot be deleted.'
            ], 403);
        }

        try {
            // Check if role has users assigned
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role. There are users assigned to this role.'
                ], 400);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role details with permissions
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');
        return response()->json([
            'success' => true,
            'data' => $role
        ], 200);
    }

    /**
     * Assign permissions to a user (SuperAdmin can assign to Admin and Staff)
     */
    public function assignUserPermissions(Request $request, \App\Models\User $user): JsonResponse
    {
        $currentUser = auth()->user();
        
        // Check permission to edit users
        if (!$currentUser->can('edit users')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to assign permissions to users.'
            ], 403);
        }
        
        // Prevent users from changing their own permissions
        if ($user->id === $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own permissions.'
            ], 403);
        }
        
        // Permissions can only be assigned to users with admin or staff dashboard access
        if (!$user->can('view admin dashboard') && !$user->can('view staff dashboard')) {
            return response()->json([
                'success' => false,
                'message' => 'Permissions can only be assigned to users with admin or staff dashboard access.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $user->syncPermissions($permissions);

            return response()->json([
                'success' => true,
                'message' => 'Permissions assigned successfully.',
                'data' => $user->load('permissions')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}

