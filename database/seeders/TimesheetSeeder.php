<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientStaffAssignment;
use App\Models\Staff;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TimesheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignments = ClientStaffAssignment::where('is_active', true)
            ->with(['client', 'staff'])
            ->get();

        if ($assignments->isEmpty()) {
            $this->command->warn('No active client-staff assignments found. Please run ClientStaffAssignmentSeeder first.');
            return;
        }

        // Generate timesheets for the last 30 days
        for ($day = 0; $day < 30; $day++) {
            $workDate = Carbon::now()->subDays($day);

            // Skip weekends (optional - you can remove this if you work weekends)
            if ($workDate->isWeekend() && rand(1, 3) > 1) {
                continue;
            }

            // Select random assignments for this day
            $dailyAssignments = $assignments->random(min(rand(3, 8), $assignments->count()));

            foreach ($dailyAssignments as $assignment) {
                // Check if timesheet already exists for this day
                $exists = Timesheet::where('staff_id', $assignment->staff_id)
                    ->where('client_id', $assignment->client_id)
                    ->where('work_date', $workDate->toDateString())
                    ->exists();

                if (!$exists) {
                    // Generate clock in time (between 6 AM and 10 AM)
                    $clockInHour = rand(6, 10);
                    $clockInMinute = rand(0, 59);
                    $clockInTime = $workDate->copy()->setTime($clockInHour, $clockInMinute);

                    // Generate clock out time (4-8 hours after clock in)
                    $hoursWorked = rand(4, 8);
                    $clockOutTime = $clockInTime->copy()->addHours($hoursWorked);

                    // Calculate payable hours (max assigned hours for this assignment)
                    $assignedHours = $assignment->assigned_weekly_hours / 5; // Assuming 5 working days
                    $payableHours = min($hoursWorked, $assignedHours);

                    // Get admin user for approval (randomly approve some)
                    $adminUser = User::role('Admin')->first();
                    $isApproved = rand(1, 3) > 1; // 66% approved

                    $timesheet = Timesheet::create([
                        'staff_id' => $assignment->staff_id,
                        'client_id' => $assignment->client_id,
                        'work_date' => $workDate->toDateString(),
                        'clock_in_time' => $clockInTime,
                        'clock_out_time' => $clockOutTime,
                        'hours_worked' => $hoursWorked,
                        'payable_hours' => $payableHours,
                        'notes' => rand(1, 3) > 2 ? 'Completed all assigned tasks. Client satisfied.' : null,
                        'is_approved' => $isApproved,
                        'approved_by' => $isApproved ? $adminUser?->id : null,
                        'approved_at' => $isApproved ? Carbon::now()->subDays($day - 1) : null,
                    ]);
                }
            }
        }

        $this->command->info('Timesheets seeded successfully!');
    }
}

