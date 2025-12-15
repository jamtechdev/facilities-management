<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Staff;
use App\DataTables\LeadDataTable;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Services\LeadService;
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
        return $dataTable->render('admin.leads.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staff = Staff::where('is_active', true)->get();
        return view('admin.leads.create', compact('staff'));
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
                'redirect' => route('admin.leads.index')
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
        $lead->load([
            'assignedStaff',
            'convertedToClient',
            'communications.user',
            'documents.uploadedBy',
            'followUpTasks.completedBy',
            'feedback'
        ]);

        $staff = Staff::where('is_active', true)->get();

        return view('admin.leads.show', compact('lead', 'staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        $staff = Staff::where('is_active', true)->get();
        return view('admin.leads.edit', compact('lead', 'staff'));
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
                'redirect' => route('admin.leads.show', $lead)
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
     * Convert lead to client (Route method)
     */
    public function convertToClient(Request $request, Lead $lead): JsonResponse
    {
        $user = auth()->user();
        
        // Check permission - SuperAdmin or user with 'convert leads' permission
        if (!$user->hasRole('SuperAdmin') && !$user->can('convert leads')) {
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
                'redirect' => route('admin.clients.show', $client->id)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
