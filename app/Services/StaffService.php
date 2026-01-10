<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StaffService
{
    /**
     * Create a new staff member
     */
    public function create(array $data): Staff
    {
        return DB::transaction(function() use ($data) {
            // Handle password
            $password = $data['password'] ?? 'password';
            unset($data['password']);

            // Create user if email provided and user doesn't exist
            if (isset($data['email']) && !isset($data['user_id'])) {
                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'name' => $data['name'],
                        'password' => Hash::make($password),
                    ]
                );

                // Update password if user already exists
                if ($user->wasRecentlyCreated === false) {
                    $user->update(['password' => Hash::make($password)]);
                }

                // Assign Staff role if not already assigned
                if (!$user->hasRole('Staff')) {
                    $user->assignRole('Staff');
                }

                $data['user_id'] = $user->id;
            }

            return Staff::create($data);
        });
    }

    /**
     * Update an existing staff member
     */
    public function update(Staff $staff, array $data): Staff
    {
        return DB::transaction(function() use ($staff, $data) {
            // Ensure we have the user relationship loaded
            if (!$staff->relationLoaded('user')) {
                $staff->load('user');
            }

            // Handle password update - ONLY update the staff's user, not the logged-in admin
            if (isset($data['password']) && !empty(trim($data['password']))) {
                if ($staff->user) {
                    // Update the staff member's user password, not the admin's
                    $staff->user->update(['password' => Hash::make($data['password'])]);
                } else {
                    // If staff doesn't have a user, create one
                    $user = User::create([
                        'name' => $data['name'] ?? $staff->name,
                        'email' => $data['email'] ?? $staff->email,
                        'password' => Hash::make($data['password']),
                    ]);
                    $user->assignRole('Staff');
                    $data['user_id'] = $user->id;
                }
                unset($data['password']);
            }

            // Update user if email changed
            if (isset($data['email']) && $staff->user && $staff->user->email !== $data['email']) {
                $staff->user->update(['email' => $data['email']]);
            }

            // Update user name if changed
            if (isset($data['name']) && $staff->user) {
                $staff->user->update(['name' => $data['name']]);
            }

            $staff->update($data);
            return $staff->fresh();
        });
    }

    /**
     * Delete a staff member
     */
    public function delete(Staff $staff): bool
    {
        return DB::transaction(function() use ($staff) {
            return $staff->delete();
        });
    }

    /**
     * Assign staff to client
     */
    public function assignToClient(Staff $staff, int $clientId, array $data = []): void
    {
        DB::transaction(function() use ($staff, $clientId, $data) {
            $staff->clients()->syncWithoutDetaching([
                $clientId => array_merge([
                    'assigned_weekly_hours' => $data['assigned_weekly_hours'] ?? 0,
                    'assigned_monthly_hours' => $data['assigned_monthly_hours'] ?? 0,
                    'assignment_start_date' => $data['assignment_start_date'] ?? now()->format('Y-m-d'),
                    'assignment_end_date' => $data['assignment_end_date'] ?? null,
                    'is_active' => true,
                ], $data)
            ]);
        });
    }

    /**
     * Remove staff from client
     */
    public function removeFromClient(Staff $staff, int $clientId): void
    {
        DB::transaction(function() use ($staff, $clientId) {
            $staff->clients()->updateExistingPivot($clientId, ['is_active' => false]);
        });
    }
}
