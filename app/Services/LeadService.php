<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadService
{
    /**
     * Create a new lead
     */
    public function create(array $data): Lead
    {
        return DB::transaction(function() use ($data) {
            return Lead::create($data);
        });
    }

    /**
     * Update an existing lead
     */
    public function update(Lead $lead, array $data): Lead
    {
        return DB::transaction(function() use ($lead, $data) {
            $lead->update($data);

            // Auto-convert to client if stage is qualified
            if (isset($data['stage']) && $data['stage'] === 'qualified' && !$lead->converted_to_client_id) {
                $this->convertToClient($lead);
            }

            return $lead->fresh();
        });
    }

    /**
     * Delete a lead
     */
    public function delete(Lead $lead): bool
    {
        return DB::transaction(function() use ($lead) {
            return $lead->delete();
        });
    }

    /**
     * Convert lead to client
     */
    public function convertToClient(Lead $lead): Client
    {
        if ($lead->converted_to_client_id) {
            throw new \Exception('Lead already converted to client.');
        }

        return DB::transaction(function() use ($lead) {
            $client = Client::create([
                'company_name' => $lead->company ?? $lead->name,
                'contact_person' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'city' => $lead->city,
                'lead_id' => $lead->id,
                'notes' => $lead->notes,
            ]);

            $lead->update([
                'converted_to_client_id' => $client->id,
                'converted_at' => now(),
            ]);

            Log::info('Lead converted to client', [
                'lead_id' => $lead->id,
                'client_id' => $client->id
            ]);

            return $client;
        });
    }

    /**
     * Get leads by stage
     */
    public function getByStage(string $stage)
    {
        return Lead::where('stage', $stage)->latest()->get();
    }

    /**
     * Get leads by assigned staff
     */
    public function getByStaff(int $staffId)
    {
        return Lead::where('assigned_staff_id', $staffId)->latest()->get();
    }
}

