<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffIds = Staff::pluck('id')->toArray();

        if (empty($staffIds)) {
            $this->command->warn('No staff found. Please run StaffSeeder first.');
            return;
        }

        $leadsData = [
            // New Leads
            [
                'name' => 'Robert Chen',
                'company' => 'TechCorp Solutions',
                'designation' => 'Facilities Manager',
                'email' => 'robert.chen@techcorp.com',
                'phone' => '+1-555-1001',
                'city' => 'New York',
                'source' => 'Website',
                'stage' => 'new_lead',
                'assigned_staff_id' => $staffIds[0] ?? null,
                'notes' => 'Interested in weekly office cleaning services. Building size: 10,000 sq ft.',
            ],
            [
                'name' => 'Lisa Anderson',
                'company' => 'Green Valley School',
                'designation' => 'Administrator',
                'email' => 'lisa.anderson@greenvalley.edu',
                'phone' => '+1-555-1002',
                'city' => 'Los Angeles',
                'source' => 'Campaign',
                'stage' => 'new_lead',
                'assigned_staff_id' => $staffIds[1] ?? null,
                'notes' => 'School with 50 classrooms. Needs daily cleaning service.',
            ],
            [
                'name' => 'James Wilson',
                'company' => 'Metro Office Complex',
                'designation' => 'Property Manager',
                'email' => 'james.wilson@metrooffice.com',
                'phone' => '+1-555-1003',
                'city' => 'Chicago',
                'source' => 'Website',
                'stage' => 'new_lead',
                'assigned_staff_id' => $staffIds[2] ?? null,
                'notes' => 'Large office complex. Multiple buildings. Requesting quote.',
            ],
            // In Progress Leads
            [
                'name' => 'Patricia Martinez',
                'company' => 'City Hospital',
                'designation' => 'Operations Director',
                'email' => 'patricia.martinez@cityhospital.com',
                'phone' => '+1-555-1004',
                'city' => 'Houston',
                'source' => 'Referral',
                'stage' => 'in_progress',
                'assigned_staff_id' => $staffIds[0] ?? null,
                'notes' => 'Hospital cleaning requirements. Specialized services needed. Meeting scheduled.',
            ],
            [
                'name' => 'Thomas Taylor',
                'company' => 'Retail Plaza',
                'designation' => 'Facilities Coordinator',
                'email' => 'thomas.taylor@retailplaza.com',
                'phone' => '+1-555-1005',
                'city' => 'Phoenix',
                'source' => 'Website',
                'stage' => 'in_progress',
                'assigned_staff_id' => $staffIds[1] ?? null,
                'notes' => 'Shopping plaza with 20 stores. Needs evening cleaning.',
            ],
            // Qualified Leads
            [
                'name' => 'Jennifer Lee',
                'company' => 'Corporate Tower',
                'designation' => 'Building Manager',
                'email' => 'jennifer.lee@corporatetower.com',
                'phone' => '+1-555-1006',
                'city' => 'New York',
                'source' => 'Website',
                'stage' => 'qualified',
                'assigned_staff_id' => $staffIds[2] ?? null,
                'notes' => 'Ready to sign contract. 30-story building. 5-year agreement.',
            ],
            [
                'name' => 'Christopher Moore',
                'company' => 'University Campus',
                'designation' => 'Facilities Director',
                'email' => 'christopher.moore@university.edu',
                'phone' => '+1-555-1007',
                'city' => 'Philadelphia',
                'source' => 'Campaign',
                'stage' => 'qualified',
                'assigned_staff_id' => $staffIds[0] ?? null,
                'notes' => 'Campus-wide cleaning. Multiple buildings. Contract approved.',
            ],
            // Not Qualified Leads
            [
                'name' => 'Amanda White',
                'company' => 'Small Business Inc',
                'designation' => 'Owner',
                'email' => 'amanda.white@smallbiz.com',
                'phone' => '+1-555-1008',
                'city' => 'Chicago',
                'source' => 'Website',
                'stage' => 'not_qualified',
                'assigned_staff_id' => null,
                'notes' => 'Budget too low. Only 500 sq ft. Not suitable for our services.',
            ],
            // Junk Leads
            [
                'name' => 'Test User',
                'company' => 'Test Company',
                'designation' => 'Test',
                'email' => 'test@test.com',
                'phone' => '+1-555-9999',
                'city' => 'Test City',
                'source' => 'Website',
                'stage' => 'junk',
                'assigned_staff_id' => null,
                'notes' => 'Spam/Test entry. Marked as junk.',
            ],
        ];

        foreach ($leadsData as $data) {
            // Create user for lead if needed
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );

            // Assign Lead role if not already assigned
            if (!$user->hasRole('Lead')) {
                $user->assignRole('Lead');
            }

            // Create lead profile if it doesn't exist (for users with Lead role)
            if (!$user->lead) {
                // Lead record will be created below
            }

            // Create lead
            Lead::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'user_id' => $user->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 90)),
                ])
            );
        }

        $this->command->info('Leads seeded successfully!');
    }
}

