<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');
        $this->command->info('');

        // Step 1: Create roles, permissions, and base users
        $this->command->info('Step 1: Creating roles, permissions, and users...');
        $this->call([
            UserRoleAndPermissionSeeder::class,
        ]);
        $this->command->info('');

        // Step 2: Create staff
        $this->command->info('Step 2: Creating staff...');
        $this->call([
            StaffSeeder::class,
        ]);
        $this->command->info('');

        // Step 3: Create leads
        $this->command->info('Step 3: Creating leads...');
        $this->call([
            LeadSeeder::class,
        ]);
        $this->command->info('');

        // Step 4: Create clients (some converted from leads)
        $this->command->info('Step 4: Creating clients...');
        $this->call([
            ClientSeeder::class,
        ]);
        $this->command->info('');

        // Step 5: Assign staff to clients
        $this->command->info('Step 5: Assigning staff to clients...');
        $this->call([
            ClientStaffAssignmentSeeder::class,
        ]);
        $this->command->info('');

        // Step 6: Create timesheets
        $this->command->info('Step 6: Creating timesheets...');
        $this->call([
            TimesheetSeeder::class,
        ]);
        $this->command->info('');

        // Step 7: Create job photos
        $this->command->info('Step 7: Creating job photos...');
        $this->call([
            JobPhotoSeeder::class,
        ]);
        $this->command->info('');

        // Step 8: Create communications
        $this->command->info('Step 8: Creating communications...');
        $this->call([
            CommunicationSeeder::class,
        ]);
        $this->command->info('');

        // Step 9: Create documents
        $this->command->info('Step 9: Creating documents...');
        $this->call([
            DocumentSeeder::class,
        ]);
        $this->command->info('');

        // Step 10: Create follow-up tasks
        $this->command->info('Step 10: Creating follow-up tasks...');
        $this->call([
            FollowUpTaskSeeder::class,
        ]);
        $this->command->info('');

        // Step 11: Create feedback
        $this->command->info('Step 11: Creating feedback...');
        $this->call([
            FeedbackSeeder::class,
        ]);
        $this->command->info('');

        // Step 12: Create invoices
        $this->command->info('Step 12: Creating invoices...');
        $this->call([
            InvoiceSeeder::class,
        ]);
        $this->command->info('');

        // Step 13: Create inventory
        $this->command->info('Step 13: Creating inventory...');
        $this->call([
            InventorySeeder::class,
        ]);
        $this->command->info('');

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('Admin: admin@keystone.com / password');
        $this->command->info('Staff: staff@keystone.com / password');
        $this->command->info('Super Admin: superadmin@keystone.com / password');
    }
}
