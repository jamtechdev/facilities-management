<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Feedback;
use App\Models\Lead;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $leads = Lead::whereIn('stage', ['in_progress', 'qualified'])->get();

        $feedbackMessages = [
            'Excellent service! The cleaning staff is very professional and thorough.',
            'Very satisfied with the cleaning quality. Would definitely recommend.',
            'Great job! The office looks spotless after every cleaning.',
            'The team is always on time and does excellent work.',
            'Very happy with the service. Clean and efficient.',
            'Outstanding cleaning service. Highly professional staff.',
            'The cleaning quality has been consistently excellent.',
            'Very pleased with the attention to detail.',
        ];

        // Feedback for clients
        foreach ($clients->take(5) as $client) {
            $numFeedbacks = rand(1, 3);
            
            for ($i = 0; $i < $numFeedbacks; $i++) {
                Feedback::create([
                    'email' => $client->email,
                    'name' => $client->contact_person,
                    'company' => $client->company_name,
                    'message' => $feedbackMessages[array_rand($feedbackMessages)],
                    'rating' => rand(4, 5), // Mostly positive ratings
                    'client_id' => $client->id,
                    'lead_id' => null,
                    'is_processed' => rand(1, 2) > 1, // 50% processed
                    'created_at' => Carbon::now()->subDays(rand(1, 60)),
                ]);
            }
        }

        // Feedback for leads
        foreach ($leads->take(3) as $lead) {
            Feedback::create([
                'email' => $lead->email,
                'name' => $lead->name,
                'company' => $lead->company,
                'message' => 'Interested in learning more about your cleaning services.',
                'rating' => null,
                'client_id' => null,
                'lead_id' => $lead->id,
                'is_processed' => false,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('Feedback seeded successfully!');
    }
}

