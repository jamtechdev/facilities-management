<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Client;
use App\Models\FollowUpTask;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeadService
{
    /**
     * Create a new lead
     */
    public function create(array $data): Lead
    {
        return DB::transaction(function() use ($data) {
            // Ensure assigned_staff_id is set if provided
            if (isset($data['assigned_staff_id']) && empty($data['assigned_staff_id'])) {
                $data['assigned_staff_id'] = null;
            }

            $data['user_id'] = auth()->id();
            $lead = Lead::create($data);

            // Create automated follow-up tasks (30/60/90 days)
            $this->createFollowUpTasks($lead);

            // Send notification to admins about new lead (with error handling)
            try {
                $notificationService = app(NotificationService::class);
                $notificationService->notifyAdminsNewLead($lead);
            } catch (\Exception $e) {
                // Log error but don't fail lead creation
                Log::error('Failed to send lead notification', [
                    'lead_id' => $lead->id,
                    'error' => $e->getMessage()
                ]);
            }

            return $lead;
        });
    }

    /**
     * Create automated follow-up tasks for a lead
     */
    protected function createFollowUpTasks(Lead $lead): void
    {
        $reminderDays = [
            FollowUpTask::DAY_30 => 'Send helpful content or schedule a 10-minute discovery call',
            FollowUpTask::DAY_60 => 'Send helpful content or schedule a 10-minute discovery call',
            FollowUpTask::DAY_90 => 'Send helpful content or schedule a 10-minute discovery call',
        ];

        foreach ($reminderDays as $day => $suggestion) {

            FollowUpTask::create([
                'lead_id' => $lead->id,
                'reminder_day' => $day,
                'suggestion' => $suggestion,
                'due_date' => Carbon::now()->addDays($day),
                'is_completed' => false,
            ]);
        }
    }

    /**
     * Update an existing lead
     */
    public function update(Lead $lead, array $data): Lead
    {
        return DB::transaction(function() use ($lead, $data) {
            // Only update the lead - do NOT auto-convert to client
            // Conversion should be done manually via the "Convert to Client" button
            $lead->update($data);

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
     * Migrates all lead data: notes, documents, communications, feedback
     */
    public function convertToClient(Lead $lead): Client
    {
        if ($lead->converted_to_client_id) {
            throw new \Exception('Lead already converted to client.');
        }

        return DB::transaction(function() use ($lead) {
            // Create client from lead data
            $client = Client::create([
                'user_id' => $lead->user_id, // Assign the user who owns the lead
                'company_name' => $lead->company ?? $lead->name,
                'contact_person' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'city' => $lead->city,
                'lead_id' => $lead->id,
                'notes' => $lead->notes,
                'is_active' => true,
            ]);

            // Assign staff from lead to client if staff is assigned to lead
            if ($lead->assigned_staff_id) {
                $client->staff()->attach($lead->assigned_staff_id, [
                    'is_active' => true,
                    'assignment_start_date' => now(),
                ]);
            }

            // Migrate all communications
            foreach ($lead->communications as $communication) {
                $communication->update([
                    'communicable_type' => Client::class,
                    'communicable_id' => $client->id,
                ]);
            }

            // Migrate all documents
            foreach ($lead->documents as $document) {
                $document->update([
                    'documentable_type' => Client::class,
                    'documentable_id' => $client->id,
                ]);
            }

            // Migrate all feedback
            foreach ($lead->feedback as $feedback) {
                $feedback->update([
                    'client_id' => $client->id,
                    'lead_id' => null, // Remove lead association
                ]);
            }

            // Delete follow-up tasks (lead-specific, not needed for client)
            $lead->followUpTasks()->delete();

            // Update lead to mark as converted and remove data
            $lead->update([
                'converted_to_client_id' => $client->id,
                'converted_at' => now(),
                'notes' => null, // Remove notes as they're now in client
            ]);

            Log::info('Lead converted to client with all data migrated', [
                'lead_id' => $lead->id,
                'client_id' => $client->id,
                'communications_migrated' => $lead->communications->count(),
                'documents_migrated' => $lead->documents->count(),
                'feedback_migrated' => $lead->feedback->count(),
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
