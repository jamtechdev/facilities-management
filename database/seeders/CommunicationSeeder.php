<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Communication;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CommunicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leads = Lead::all();
        $clients = Client::all();
        $users = User::role(['Admin', 'Staff'])->get();

        if ($users->isEmpty()) {
            $this->command->warn('No admin or staff users found.');
            return;
        }

        $communicationTypes = ['call', 'email', 'meeting', 'note'];

        // Add communications for leads
        foreach ($leads->take(8) as $lead) {
            $numCommunications = rand(2, 5);
            
            for ($i = 0; $i < $numCommunications; $i++) {
                $type = $communicationTypes[array_rand($communicationTypes)];
                $user = $users->random();
                $createdAt = Carbon::now()->subDays(rand(1, 90));

                $communicationData = [
                    'communicable_type' => Lead::class,
                    'communicable_id' => $lead->id,
                    'type' => $type,
                    'message' => $this->getMessageForType($type, $lead),
                    'user_id' => $user->id,
                    'is_sent' => in_array($type, ['email']) ? (rand(1, 2) > 1) : false,
                    'created_at' => $createdAt,
                ];

                if ($type === 'email') {
                    $communicationData['subject'] = 'Re: Cleaning Services Inquiry - ' . $lead->company;
                    $communicationData['email_to'] = $lead->email;
                    $communicationData['email_from'] = $user->email;
                }

                if ($type === 'meeting') {
                    $communicationData['scheduled_at'] = $createdAt->copy()->addDays(rand(1, 7));
                }

                Communication::create($communicationData);
            }
        }

        // Add communications for clients
        foreach ($clients->take(5) as $client) {
            $numCommunications = rand(3, 6);
            
            for ($i = 0; $i < $numCommunications; $i++) {
                $type = $communicationTypes[array_rand($communicationTypes)];
                $user = $users->random();
                $createdAt = Carbon::now()->subDays(rand(1, 60));

                $communicationData = [
                    'communicable_type' => Client::class,
                    'communicable_id' => $client->id,
                    'type' => $type,
                    'message' => $this->getMessageForType($type, $client),
                    'user_id' => $user->id,
                    'is_sent' => in_array($type, ['email']) ? (rand(1, 2) > 1) : false,
                    'created_at' => $createdAt,
                ];

                if ($type === 'email') {
                    $communicationData['subject'] = 'Re: Service Update - ' . $client->company_name;
                    $communicationData['email_to'] = $client->email;
                    $communicationData['email_from'] = $user->email;
                }

                if ($type === 'meeting') {
                    $communicationData['scheduled_at'] = $createdAt->copy()->addDays(rand(1, 7));
                }

                Communication::create($communicationData);
            }
        }

        $this->command->info('Communications seeded successfully!');
    }

    private function getMessageForType($type, $entity)
    {
        $messages = [
            'call' => [
                "Called to discuss cleaning requirements. Client showed interest in our services.",
                "Follow-up call made. Discussed pricing and schedule options.",
                "Phone conversation regarding service details. Client asked about availability.",
            ],
            'email' => [
                "Sent email with service proposal and pricing information.",
                "Follow-up email sent with additional information about our cleaning services.",
                "Email correspondence regarding contract terms and service schedule.",
            ],
            'meeting' => [
                "Scheduled meeting to discuss cleaning needs and provide site walkthrough.",
                "In-person meeting conducted. Reviewed facility and discussed service requirements.",
                "Meeting scheduled to finalize contract details and service agreement.",
            ],
            'note' => [
                "Client mentioned they need specialized cleaning for medical equipment areas.",
                "Noted that client prefers evening cleaning shifts due to business hours.",
                "Important: Client requested eco-friendly cleaning products only.",
            ],
        ];

        $entityName = $entity->company ?? $entity->company_name ?? $entity->name;
        $message = $messages[$type][array_rand($messages[$type])];
        
        return str_replace('Client', $entityName, $message);
    }
}

