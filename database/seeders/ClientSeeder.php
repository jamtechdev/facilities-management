<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Lead;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get qualified leads that can be converted
        $qualifiedLeads = Lead::where('stage', 'qualified')->get();

        $clientsData = [
            [
                'company_name' => 'Global Industries Ltd',
                'contact_person' => 'Michael Thompson',
                'email' => 'michael.thompson@globalindustries.com',
                'phone' => '+1-555-2001',
                'address' => '1000 Business Park Drive, New York, NY 10001',
                'city' => 'New York',
                'postal_code' => '10001',
                'agreed_weekly_hours' => 40.00,
                'agreed_monthly_hours' => 160.00,
                'billing_frequency' => 'monthly',
                'lead_id' => null,
                'notes' => 'Long-term client. 5-year contract. Very satisfied with services.',
                'is_active' => true,
            ],
            [
                'company_name' => 'Education First Academy',
                'contact_person' => 'Susan Rodriguez',
                'email' => 'susan.rodriguez@edufirst.edu',
                'phone' => '+1-555-2002',
                'address' => '500 Education Boulevard, Los Angeles, CA 90001',
                'city' => 'Los Angeles',
                'postal_code' => '90001',
                'agreed_weekly_hours' => 35.00,
                'agreed_monthly_hours' => 140.00,
                'billing_frequency' => 'monthly',
                'lead_id' => null,
                'notes' => 'School with 30 classrooms. Daily cleaning required.',
                'is_active' => true,
            ],
            [
                'company_name' => 'Medical Center',
                'contact_person' => 'Dr. Robert Kim',
                'email' => 'robert.kim@medicalcenter.com',
                'phone' => '+1-555-2003',
                'address' => '200 Health Avenue, Chicago, IL 60601',
                'city' => 'Chicago',
                'postal_code' => '60601',
                'agreed_weekly_hours' => 60.00,
                'agreed_monthly_hours' => 240.00,
                'billing_frequency' => 'monthly',
                'lead_id' => null,
                'notes' => 'Hospital cleaning. Specialized medical facility cleaning.',
                'is_active' => true,
            ],
            [
                'company_name' => 'Shopping Mall Complex',
                'contact_person' => 'Daniel Park',
                'email' => 'daniel.park@mallcomplex.com',
                'phone' => '+1-555-2004',
                'address' => '300 Retail Street, Houston, TX 77001',
                'city' => 'Houston',
                'postal_code' => '77001',
                'agreed_weekly_hours' => 50.00,
                'agreed_monthly_hours' => 200.00,
                'billing_frequency' => 'monthly',
                'lead_id' => null,
                'notes' => 'Large shopping mall. Evening cleaning shifts.',
                'is_active' => true,
            ],
            [
                'company_name' => 'Tech Startup Hub',
                'contact_person' => 'Jessica Liu',
                'email' => 'jessica.liu@techhub.com',
                'phone' => '+1-555-2005',
                'address' => '150 Innovation Way, Phoenix, AZ 85001',
                'city' => 'Phoenix',
                'postal_code' => '85001',
                'agreed_weekly_hours' => 20.00,
                'agreed_monthly_hours' => 80.00,
                'billing_frequency' => 'monthly',
                'lead_id' => null,
                'notes' => 'Modern office space. Flexible cleaning schedule.',
                'is_active' => true,
            ],
        ];

        // Convert some qualified leads to clients (without creating users)
        foreach ($qualifiedLeads->take(2) as $index => $lead) {
            $client = Client::create([
                'user_id' => null,
                'company_name' => $lead->company ?? 'Company ' . ($index + 1),
                'contact_person' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'city' => $lead->city,
                'agreed_weekly_hours' => rand(20, 40),
                'agreed_monthly_hours' => rand(80, 160),
                'billing_frequency' => ['weekly', 'monthly'][rand(0, 1)],
                'lead_id' => $lead->id,
                'notes' => 'Converted from qualified lead: ' . $lead->notes,
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(rand(30, 60)),
            ]);

            // Update lead conversion
            $lead->update([
                'converted_to_client_id' => $client->id,
                'converted_at' => $client->created_at,
            ]);
        }

        // Create additional clients (without creating users)
        foreach ($clientsData as $data) {
            Client::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'user_id' => null,
                    'created_at' => Carbon::now()->subDays(rand(60, 180)),
                ])
            );
        }

        $this->command->info('Clients seeded successfully!');
    }
}
