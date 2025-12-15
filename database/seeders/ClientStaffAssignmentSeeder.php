<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientStaffAssignment;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ClientStaffAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $staff = Staff::all();

        if ($clients->isEmpty() || $staff->isEmpty()) {
            $this->command->warn('No clients or staff found. Please run ClientSeeder and StaffSeeder first.');
            return;
        }

        $assignments = [];

        // Assign multiple staff to each client
        foreach ($clients as $client) {
            $numStaff = rand(1, min(3, $staff->count()));
            $selectedStaff = $staff->random($numStaff);

            foreach ($selectedStaff as $staffMember) {
                // Check if assignment already exists
                $exists = ClientStaffAssignment::where('client_id', $client->id)
                    ->where('staff_id', $staffMember->id)
                    ->exists();

                if (!$exists) {
                    $weeklyHours = rand(10, 30);
                    $monthlyHours = $weeklyHours * 4;

                    ClientStaffAssignment::create([
                        'client_id' => $client->id,
                        'staff_id' => $staffMember->id,
                        'assigned_weekly_hours' => $weeklyHours,
                        'assigned_monthly_hours' => $monthlyHours,
                        'assignment_start_date' => Carbon::now()->subDays(rand(30, 180)),
                        'assignment_end_date' => null,
                        'is_active' => true,
                        'notes' => "Assigned to {$client->company_name} for regular cleaning services.",
                    ]);
                }
            }
        }

        $this->command->info('Client-Staff assignments seeded successfully!');
    }
}

