<?php

namespace App\Observers;

use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Support\Facades\Log;

class LeadObserver
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    /**
     * Handle the Lead "updated" event.
     * Automatically convert lead to client when stage becomes "qualified"
     */
    public function updated(Lead $lead): void
    {
        // Check if stage was changed to "qualified" and lead hasn't been converted yet
        if ($lead->isDirty('stage')
            && $lead->stage === Lead::STAGE_QUALIFIED
            && !$lead->converted_to_client_id) {

            try {
                // Store lead data before conversion (lead will be deleted during conversion)
                $leadId = $lead->id;
                $leadStage = $lead->stage;

                // Automatically convert lead to client
                $client = $this->leadService->convertToClient($lead);

                Log::info('Lead automatically converted to client via observer', [
                    'lead_id' => $leadId,
                    'client_id' => $client->id,
                    'stage' => $leadStage
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the update
                Log::error('Failed to auto-convert lead to client', [
                    'lead_id' => $lead->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
