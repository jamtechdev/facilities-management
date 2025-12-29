<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Staff;
use App\DataTables\LeadDataTable;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Services\LeadService;
use App\Helpers\RouteHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(LeadDataTable $dataTable)
    {
        $viewPrefix = RouteHelper::getViewPrefix();
        return $dataTable->render($viewPrefix . '.leads.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staff = Staff::where('is_active', true)->get();
        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.leads.create', compact('staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        try {
            $lead = $this->leadService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully.',
                'redirect' => RouteHelper::url('leads.index')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        // Check permission to view lead details
        if (!auth()->user()->can('view lead details')) {
            abort(403, 'You do not have permission to view lead details.');
        }
        $lead->load([
            'assignedStaff',
            'convertedToClient',
            'communications.user',
            'documents.uploadedBy',
            'followUpTasks.completedBy',
            'feedback'
        ]);

        $staff = Staff::where('is_active', true)->get();
        $viewPrefix = RouteHelper::getViewPrefix();

        return view($viewPrefix . '.leads.show', compact('lead', 'staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        $staff = Staff::where('is_active', true)->get();
        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.leads.edit', compact('lead', 'staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeadRequest $request, Lead $lead): JsonResponse
    {
        try {
            $this->leadService->update($lead, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully.',
                'redirect' => RouteHelper::url('leads.index')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead): JsonResponse
    {
        try {
            $this->leadService->delete($lead);

            return response()->json([
                'success' => true,
                'message' => 'Lead deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update lead stage (for inline editing in DataTable)
     * Only updates stage, does NOT convert to client
     */
    public function updateStage(Request $request, Lead $lead): JsonResponse
    {
        $user = auth()->user();

        // Only SuperAdmin (users with view roles permission) can update stage directly
        if (!$user->can('view roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update lead stage.'
            ], 403);
        }

        $validated = $request->validate([
            'stage' => 'required|in:new_lead,in_progress,qualified,not_qualified,junk'
        ]);

        try {
            // Only update stage field - do NOT trigger conversion
            // Use updateQuietly to bypass model events and observers
            // This ensures no auto-conversion happens when stage changes from DataTable
            $lead->updateQuietly(['stage' => $validated['stage']]);

            // Refresh the model to get updated data
            $lead->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Lead stage updated successfully.',
                'stage' => $lead->stage,
                'is_qualified' => $lead->stage === 'qualified',
                'is_converted' => (bool)$lead->converted_to_client_id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead stage: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert lead to client (Route method)
     */
    public function convertToClient(Request $request, Lead $lead): JsonResponse
    {
        $user = auth()->user();

        // Check permission to convert leads
        if (!$user->can('convert leads')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to convert leads.'
            ], 403);
        }

        try {
            $client = $this->leadService->convertToClient($lead);

            return response()->json([
                'success' => true,
                'message' => 'Lead converted to client successfully.',
                'redirect' => RouteHelper::url('clients.show', $client->id)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
