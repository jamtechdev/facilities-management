<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Lead;
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
            // Dashboard card/widget permissions
            'view dashboard leads card',
            'view dashboard clients card',
            'view dashboard staff card',
            'view dashboard revenue card',
            'view dashboard qualified leads card',
            'view dashboard new leads card',
            'view dashboard users card',
            'view dashboard invoices card',
            'view dashboard followup reminders card',
            'view dashboard recent activity card',
            'view dashboard lead stages graph',
            'view dashboard leads chart',
            // Lead permissions
            'view leads',
            'view lead details',
            'create leads',
            'edit leads',
            'delete leads',
            'convert leads',
            // Client permissions
            'view clients',
            'view client details',
            'create clients',
            'edit clients',
            'delete clients',
            // Staff permissions
            'view staff',
            'view staff details',
            'create staff',
            'edit staff',
            'delete staff',
            'assign staff',
            // Timesheet permissions
            'view timesheets',
            'create timesheets',
            'edit timesheets',
            'approve timesheets',
            // Invoice permissions
            'view invoices',
            'view invoice details',
            'create invoices',
            'edit invoices',
            'delete invoices',
            // User permissions
            'view users',
            'view user details',
            'create users',
            'edit users',
            'delete users',
            // Payout permissions
            'view payouts',
            'calculate payouts',
            'download payout reports',
            // Inventory permissions
            'view inventory',
            'create inventory',
            'edit inventory',
            'delete inventory',
            'assign inventory',
            // Role & Permission permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $clientRole = Role::firstOrCreate(['name' => 'Client']);
        $leadRole = Role::firstOrCreate(['name' => 'Lead']);

        // Assign ALL permissions to SuperAdmin role
        $superAdminRole->syncPermissions(Permission::all());

        // Assign ALL permissions to Admin role (same as SuperAdmin)
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

        // Create SuperAdmin user (has all permissions)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@keystone.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Assign SuperAdmin role if not already assigned
        if (!$superAdmin->hasRole('SuperAdmin')) {
            $superAdmin->assignRole('SuperAdmin');
        }

        // Create Admin user (has all permissions)
        $admin = User::firstOrCreate(
            ['email' => 'admin@keystone.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // Assign Admin role if not already assigned
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

        // Create staff profile if it doesn't exist
        if (!$staffUser->staff) {
            Staff::firstOrCreate(
                ['user_id' => $staffUser->id],
                [
                    'name' => 'Staff User',
                    'email' => 'staff@keystone.com',
                    'is_active' => true,
                ]
            );
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

        // Create client profile if it doesn't exist
        if (!$clientUser->client) {
            Client::firstOrCreate(
                ['user_id' => $clientUser->id],
                [
                    'company_name' => 'Client Company',
                    'contact_person' => 'Client User',
                    'email' => 'client@keystone.com',
                    'is_active' => true,
                ]
            );
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

        // Create lead profile if it doesn't exist
        if (!$leadUser->lead) {
            Lead::firstOrCreate(
                ['user_id' => $leadUser->id],
                [
                    'name' => 'Lead User',
                    'email' => 'lead@keystone.com',
                    'stage' => Lead::STAGE_NEW_LEAD,
                ]
            );
        }

        // Fix all existing users - create missing model records
        $this->fixExistingUsers();

        $this->command->info('Permissions, roles, and users created successfully!');
        $this->command->info('');
        $this->command->info('SuperAdmin credentials (has ALL permissions):');
        $this->command->info('Email: superadmin@keystone.com');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('Admin credentials (has ALL permissions):');
        $this->command->info('Email: admin@keystone.com');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('Staff credentials:');
        $this->command->info('Email: staff@keystone.com');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('Note: Both SuperAdmin and Admin roles have all permissions by default.');
    }

    /**
     * Fix existing users by creating missing model records
     */
    protected function fixExistingUsers(): void
    {
        // Fix Staff users
        $staffUsers = User::role('Staff')->get();
        foreach ($staffUsers as $user) {
            if (!$user->staff) {
                Staff::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_active' => true,
                    ]
                );
                $this->command->info("Created Staff record for: {$user->email}");
            }
        }

        // Fix Client users
        $clientUsers = User::role('Client')->get();
        foreach ($clientUsers as $user) {
            if (!$user->client) {
                Client::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'company_name' => $user->name . ' Company',
                        'contact_person' => $user->name,
                        'email' => $user->email,
                        'is_active' => true,
                    ]
                );
                $this->command->info("Created Client record for: {$user->email}");
            }
        }

        // Fix Lead users
        $leadUsers = User::role('Lead')->get();
        foreach ($leadUsers as $user) {
            if (!$user->lead) {
                Lead::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'email' => $user->email,
                        'stage' => Lead::STAGE_NEW_LEAD,
                    ]
                );
                $this->command->info("Created Lead record for: {$user->email}");
            }
        }
    }
}

