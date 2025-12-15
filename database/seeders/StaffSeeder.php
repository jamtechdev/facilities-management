<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffData = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@keystone.com',
                'mobile' => '+1-555-0101',
                'address' => '123 Main Street, New York, NY 10001',
                'hourly_rate' => 25.00,
                'assigned_weekly_hours' => 40.00,
                'assigned_monthly_hours' => 160.00,
                'is_active' => true,
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@keystone.com',
                'mobile' => '+1-555-0102',
                'address' => '456 Oak Avenue, Los Angeles, CA 90001',
                'hourly_rate' => 28.50,
                'assigned_weekly_hours' => 35.00,
                'assigned_monthly_hours' => 140.00,
                'is_active' => true,
            ],
            [
                'name' => 'David Johnson',
                'email' => 'david.johnson@keystone.com',
                'mobile' => '+1-555-0103',
                'address' => '789 Pine Road, Chicago, IL 60601',
                'hourly_rate' => 22.00,
                'assigned_weekly_hours' => 30.00,
                'assigned_monthly_hours' => 120.00,
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah.williams@keystone.com',
                'mobile' => '+1-555-0104',
                'address' => '321 Elm Street, Houston, TX 77001',
                'hourly_rate' => 26.00,
                'assigned_weekly_hours' => 25.00,
                'assigned_monthly_hours' => 100.00,
                'is_active' => true,
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@keystone.com',
                'mobile' => '+1-555-0105',
                'address' => '654 Maple Drive, Phoenix, AZ 85001',
                'hourly_rate' => 24.50,
                'assigned_weekly_hours' => 20.00,
                'assigned_monthly_hours' => 80.00,
                'is_active' => true,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@keystone.com',
                'mobile' => '+1-555-0106',
                'address' => '987 Cedar Lane, Philadelphia, PA 19101',
                'hourly_rate' => 27.00,
                'assigned_weekly_hours' => 15.00,
                'assigned_monthly_hours' => 60.00,
                'is_active' => true,
            ],
        ];

        foreach ($staffData as $data) {
            // Create user for staff
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );

            // Assign Staff role if not already assigned
            if (!$user->hasRole('Staff')) {
                $user->assignRole('Staff');
            }

            // Create staff profile (check by user_id first, then email)
            Staff::firstOrCreate(
                ['user_id' => $user->id],
                array_merge($data, ['user_id' => $user->id])
            );
        }

        $this->command->info('Staff seeded successfully!');
    }
}

