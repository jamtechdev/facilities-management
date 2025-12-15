<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index()
    {
        $permissions = Permission::latest()->get()->groupBy(function($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[0] ?? 'other';
        });
        
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
            'group' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully.',
                'data' => $permission
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permission->update(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully.',
                'data' => $permission
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission): JsonResponse
    {
        try {
            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission: ' . $e->getMessage()
            ], 500);
        }
    }
}

