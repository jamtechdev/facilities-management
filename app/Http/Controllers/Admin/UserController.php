<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\DataTables\UserDataTable;
use App\Helpers\RouteHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users using DataTables.
     */
    public function index(UserDataTable $dataTable)
    {
        $viewPrefix = RouteHelper::getViewPrefix();
        return $dataTable->render($viewPrefix . '.users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $currentUser = auth()->user();

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Assign default Lead role for new users
            $user->assignRole('Lead');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'redirect' => RouteHelper::url('users.index')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified user.
     */
    public function show(User $user)
    {
        // Check permission to view user details
        if (!auth()->user()->can('view user details')) {
            abort(403, 'You do not have permission to view user details.');
        }
        $user->load('roles');
        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $currentUser = auth()->user();

        // Check permission to edit users
        if (!$currentUser->can('edit users')) {
            abort(403, 'You do not have permission to edit users.');
        }

        // Prevent users from editing themselves
        if ($user->id === $currentUser->id) {
            abort(403, 'You cannot edit your own user account.');
        }

        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $currentUser = auth()->user();

        // Check permission to edit users
        if (!$currentUser->can('edit users')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit users.'
            ], 403);
        }

        // Prevent users from editing themselves
        if ($user->id === $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot edit your own user account.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            // Keep existing roles - no role changes through this form

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'redirect' => RouteHelper::url('users.index')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        $currentUser = auth()->user();

        // Check permission to delete users
        if (!$currentUser->can('delete users')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete users.'
            ], 403);
        }

        // Prevent users from deleting themselves
        if ($user->id === $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own user account.'
            ], 403);
        }

        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
}
