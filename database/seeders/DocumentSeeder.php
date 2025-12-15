<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Document;
use App\Models\Lead;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leads = Lead::all();
        $clients = Client::all();
        $staff = Staff::all();
        $users = User::role('Admin')->get();

        if ($users->isEmpty()) {
            $adminUser = User::first();
        } else {
            $adminUser = $users->first();
        }

        // Documents for Leads
        $leadDocumentTypes = ['proposal', 'agreement', 'signed_form', 'other'];
        foreach ($leads->where('stage', 'in_progress')->take(5) as $lead) {
            $numDocs = rand(1, 3);
            for ($i = 0; $i < $numDocs; $i++) {
                $docType = $leadDocumentTypes[array_rand($leadDocumentTypes)];
                Document::create([
                    'documentable_type' => Lead::class,
                    'documentable_id' => $lead->id,
                    'name' => ucfirst($docType) . ' - ' . $lead->company,
                    'file_path' => 'storage/documents/leads/' . $lead->id . '_' . $docType . '_' . ($i + 1) . '.pdf',
                    'file_type' => 'application/pdf',
                    'file_size' => rand(100000, 5000000), // 100KB to 5MB
                    'document_type' => $docType,
                    'description' => $this->getDescriptionForType($docType, $lead),
                    'uploaded_by' => $adminUser?->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // Documents for Clients
        $clientDocumentTypes = ['agreement', 'signed_form', 'proposal', 'other'];
        foreach ($clients->take(5) as $client) {
            $numDocs = rand(2, 4);
            for ($i = 0; $i < $numDocs; $i++) {
                $docType = $clientDocumentTypes[array_rand($clientDocumentTypes)];
                Document::create([
                    'documentable_type' => Client::class,
                    'documentable_id' => $client->id,
                    'name' => ucfirst($docType) . ' - ' . $client->company_name,
                    'file_path' => 'storage/documents/clients/' . $client->id . '_' . $docType . '_' . ($i + 1) . '.pdf',
                    'file_type' => 'application/pdf',
                    'file_size' => rand(100000, 5000000),
                    'document_type' => $docType,
                    'description' => $this->getDescriptionForType($docType, $client),
                    'uploaded_by' => $adminUser?->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 60)),
                ]);
            }
        }

        // Documents for Staff (ID, certificates, agreements)
        $staffDocumentTypes = ['id', 'certificate', 'agreement', 'other'];
        foreach ($staff->take(5) as $staffMember) {
            $numDocs = rand(1, 3);
            for ($i = 0; $i < $numDocs; $i++) {
                $docType = $staffDocumentTypes[array_rand($staffDocumentTypes)];
                Document::create([
                    'documentable_type' => Staff::class,
                    'documentable_id' => $staffMember->id,
                    'name' => ucfirst($docType) . ' - ' . $staffMember->name,
                    'file_path' => 'storage/documents/staff/' . $staffMember->id . '_' . $docType . '_' . ($i + 1) . '.pdf',
                    'file_type' => $docType === 'id' ? 'image/jpeg' : 'application/pdf',
                    'file_size' => $docType === 'id' ? rand(50000, 500000) : rand(100000, 5000000),
                    'document_type' => $docType,
                    'description' => $this->getDescriptionForType($docType, $staffMember),
                    'uploaded_by' => $adminUser?->id,
                    'created_at' => Carbon::now()->subDays(rand(30, 180)),
                ]);
            }
        }

        $this->command->info('Documents seeded successfully!');
    }

    private function getDescriptionForType($type, $entity)
    {
        $descriptions = [
            'agreement' => 'Service agreement document',
            'proposal' => 'Service proposal and pricing',
            'signed_form' => 'Signed service contract',
            'id' => 'Government issued identification',
            'certificate' => 'Professional certification',
            'other' => 'Additional document',
        ];

        $entityName = $entity->company ?? $entity->company_name ?? $entity->name;
        return ($descriptions[$type] ?? 'Document') . ' for ' . $entityName;
    }
}

