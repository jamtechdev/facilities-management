<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LeadApiController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    /**
     * Store a newly created lead from website/campaign form
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['stage'] = Lead::STAGE_NEW_LEAD;
            $data['source'] = $data['source'] ?? 'Website Form';

            $lead = $this->leadService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'data' => [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead: ' . $e->getMessage()
            ], 500);
        }
    }
}
