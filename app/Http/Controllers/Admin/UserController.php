<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\DataTables\UserDataTable;
use App\Helpers\RouteHelper;
use Illuminate\Http\Request;
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
        $roles = Role::all();
        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);
        // return response()->json(['success' => true]);
        return redirect(RouteHelper::url('users.index'))->with('success', 'User created successfully.');
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
        return response()->json($user);
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

        $roles = Role::all();
        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Role update
        $user->syncRoles([$validated['role']]);

        return redirect(RouteHelper::url('users.index'))->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        
        // Check permission to delete users
        if (!$currentUser->can('delete users')) {
            abort(403, 'You do not have permission to delete users.');
        }
        
        // Prevent users from deleting themselves
        if ($user->id === $currentUser->id) {
            abort(403, 'You cannot delete your own user account.');
        }

        $user->delete();
        return response()->json(['success' => true]);
    }
}
