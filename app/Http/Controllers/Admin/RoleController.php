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
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->latest()->get();
        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[0] ?? 'other';
        });
        
        return view('admin.roles.index', compact('roles', 'permissions'));
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

        // Prevent Admin from modifying Admin role (only SuperAdmin can, but SuperAdmin also cannot modify their own role)
        if ($role->name === 'Admin' && $user->hasRole('Admin') && !$user->hasRole('SuperAdmin')) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot modify the Admin role. Only SuperAdmin can change Admin permissions.'
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
        // Prevent deleting SuperAdmin and Admin roles
        if (in_array($role->name, ['SuperAdmin', 'Admin'])) {
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
     * Assign permissions to a user (SuperAdmin can assign to Admin, Admin can assign to Staff)
     */
    public function assignUserPermissions(Request $request, \App\Models\User $user): JsonResponse
    {
        $currentUser = auth()->user();
        
        // Prevent SuperAdmin from changing their own permissions
        if ($user->id === $currentUser->id && $currentUser->hasRole('SuperAdmin')) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own permissions. SuperAdmin permissions cannot be modified.'
            ], 403);
        }

        // Prevent Admin from changing their own permissions
        if ($user->id === $currentUser->id && $currentUser->hasRole('Admin') && !$currentUser->hasRole('SuperAdmin')) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own permissions. Only SuperAdmin can modify Admin permissions.'
            ], 403);
        }
        
        // Only SuperAdmin can assign permissions to Admin
        if ($user->hasRole('Admin') && !$currentUser->hasRole('SuperAdmin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only SuperAdmin can assign permissions to Admin users.'
            ], 403);
        }

        // Admin can only assign permissions to Staff
        if ($user->hasRole('Staff') && $currentUser->hasRole('Admin') && !$currentUser->hasRole('SuperAdmin')) {
            // Admin can assign permissions to Staff
        } elseif (!$currentUser->hasRole('SuperAdmin')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to assign permissions to this user.'
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

