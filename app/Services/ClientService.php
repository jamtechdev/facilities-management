<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ClientService
{
    /**
     * Create a new client
     */
    public function create(array $data): Client
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
                        'name' => $data['contact_person'] ?? $data['company_name'],
                        'password' => Hash::make($password),
                    ]
                );

                // Update password if user already exists
                if ($user->wasRecentlyCreated === false) {
                    $user->update(['password' => Hash::make($password)]);
                }

                // Assign Client role if not already assigned
                if (!$user->hasRole('Client')) {
                    $user->assignRole('Client');
                }

                $data['user_id'] = $user->id;
            }

            return Client::create($data);
        });
    }

    /**
     * Update an existing client
     */
    public function update(Client $client, array $data): Client
    {
        return DB::transaction(function() use ($client, $data) {
            // Ensure we have the user relationship loaded
            if (!$client->relationLoaded('user')) {
                $client->load('user');
            }

            // Handle password update - ONLY update the client's user, not the logged-in admin
            if (isset($data['password']) && !empty(trim($data['password']))) {
                if ($client->user) {
                    // Update the client's user password, not the admin's
                    $client->user->update(['password' => Hash::make($data['password'])]);
                } else {
                    // If client doesn't have a user, create one
                    $user = User::create([
                        'name' => $data['contact_person'] ?? $client->contact_person ?? $client->company_name,
                        'email' => $data['email'] ?? $client->email,
                        'password' => Hash::make($data['password']),
                    ]);
                    $user->assignRole('Client');
                    $data['user_id'] = $user->id;
                }
                unset($data['password']);
            }

            // Update user if email changed
            if (isset($data['email']) && $client->user && $client->user->email !== $data['email']) {
                $client->user->update(['email' => $data['email']]);
            }

            // Update user name if changed
            if (isset($data['contact_person']) && $client->user) {
                $client->user->update(['name' => $data['contact_person']]);
            }

            $client->update($data);
            return $client->fresh();
        });
    }

    /**
     * Delete a client
     */
    public function delete(Client $client): bool
    {
        return DB::transaction(function() use ($client) {
            return $client->delete();
        });
    }

    /**
     * Assign staff to client
     */
    public function assignStaff(Client $client, int $staffId, array $data = []): void
    {
        DB::transaction(function() use ($client, $staffId, $data) {
            $client->staff()->syncWithoutDetaching([
                $staffId => array_merge([
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
    public function removeStaff(Client $client, int $staffId): void
    {
        DB::transaction(function() use ($client, $staffId) {
            $client->staff()->updateExistingPivot($staffId, ['is_active' => false]);
        });
    }
}
