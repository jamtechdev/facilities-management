<?php

namespace Database\Seeders;

use App\Models\FollowUpTask;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FollowUpTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leads = Lead::whereIn('stage', ['new_lead', 'in_progress'])->get();

        if ($leads->isEmpty()) {
            $this->command->warn('No leads found for follow-up tasks.');
            return;
        }

        $reminderDays = [
            ['value' => '30', 'days' => 30],
            ['value' => '60', 'days' => 60],
            ['value' => '90', 'days' => 90],
        ];
        $suggestions = [
            'Send helpful content about our cleaning services',
            'Schedule a 10-minute discovery call',
            'Share case studies and client testimonials',
            'Send pricing information and service packages',
            'Follow up on previous conversation',
        ];

        foreach ($leads as $lead) {
            $leadCreatedAt = $lead->created_at ?? Carbon::now();

            // Create follow-up tasks for 30, 60, and 90 days
            foreach ($reminderDays as $reminder) {
                $dueDate = $leadCreatedAt->copy()->addDays($reminder['days']);
                
                // Only create if due date is in the past or near future
                if ($dueDate->isPast() || $dueDate->isFuture() && $dueDate->diffInDays(Carbon::now()) <= 30) {
                    $isCompleted = $dueDate->isPast() && rand(1, 3) > 1; // 66% chance if past
                    
                    $task = FollowUpTask::create([
                        'lead_id' => $lead->id,
                        'reminder_day' => $reminder['value'],
                        'suggestion' => $suggestions[array_rand($suggestions)],
                        'is_completed' => $isCompleted,
                        'due_date' => $dueDate,
                        'completed_at' => $isCompleted ? $dueDate->copy()->addDays(rand(0, 5)) : null,
                        'completed_by' => $isCompleted ? User::role('Admin')->first()?->id : null,
                        'created_at' => $leadCreatedAt->copy()->addDays($reminder['days'] - 1),
                    ]);
                }
            }
        }

        $this->command->info('Follow-up tasks seeded successfully!');
    }
}

