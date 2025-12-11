<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard permissions
            'view admin dashboard',
            'view staff dashboard',
            'view client dashboard',
            'view lead dashboard',
            // Lead permissions
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'convert leads',
            // Client permissions
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            // Staff permissions
            'view staff',
            'create staff',
            'edit staff',
            'delete staff',
            // Timesheet permissions
            'view timesheets',
            'create timesheets',
            'edit timesheets',
            'approve timesheets',
            // Invoice permissions
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $clientRole = Role::firstOrCreate(['name' => 'Client']);
        $leadRole = Role::firstOrCreate(['name' => 'Lead']);

        // Assign all permissions to Admin role
        $adminRole->syncPermissions(Permission::all());

        // Assign permissions to Staff role
        $staffRole->syncPermissions([
            'view staff dashboard',
            'view timesheets',
            'create timesheets',
            'edit timesheets',
        ]);

        // Assign permissions to Client role
        $clientRole->syncPermissions([
            'view client dashboard',
        ]);

        // Assign permissions to Lead role
        $leadRole->syncPermissions([
            'view lead dashboard',
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@keystone.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // Assign admin role if not already assigned
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        // Create staff user
        $staffUser = User::firstOrCreate(
            ['email' => 'staff@keystone.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
            ]
        );

        // Assign staff role if not already assigned
        if (!$staffUser->hasRole('Staff')) {
            $staffUser->assignRole('Staff');
        }

        // Create client user
        $clientUser = User::firstOrCreate(
            ['email' => 'client@keystone.com'],
            [
                'name' => 'Client User',
                'password' => Hash::make('password'),
            ]
        );

        // Assign client role if not already assigned
        if (!$clientUser->hasRole('Client')) {
            $clientUser->assignRole('Client');
        }

        // Create lead user
        $leadUser = User::firstOrCreate(
            ['email' => 'lead@keystone.com'],
            [
                'name' => 'Lead User',
                'password' => Hash::make('password'),
            ]
        );

        // Assign lead role if not already assigned
        if (!$leadUser->hasRole('Lead')) {
            $leadUser->assignRole('Lead');
        }

        $this->command->info('Permissions, roles, and users created successfully!');
        $this->command->info('');
        $this->command->info('Admin credentials:');
        $this->command->info('Email: admin@keystone.com');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('Staff credentials:');
        $this->command->info('Email: staff@keystone.com');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('Client credentials:');
        $this->command->info('Email: client@keystone.com');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('Lead credentials:');
        $this->command->info('Email: lead@keystone.com');
        $this->command->info('Password: password');
    }
}

