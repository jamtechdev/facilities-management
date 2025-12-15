<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Communication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    /**
     * Public feedback form submission
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'message' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function() use ($request) {
                $data = $validator->validated();
                
                // Try to find matching client first
                $client = Client::where('email', $data['email'])->first();
                
                // If no client, try to find matching lead
                $lead = null;
                if (!$client) {
                    $lead = Lead::where('email', $data['email'])->first();
                }

                // Create feedback
                $feedback = Feedback::create([
                    'email' => $data['email'],
                    'name' => $data['name'] ?? null,
                    'company' => $data['company'] ?? null,
                    'message' => $data['message'],
                    'rating' => $data['rating'] ?? null,
                    'client_id' => $client?->id,
                    'lead_id' => $lead?->id,
                    'is_processed' => false,
                ]);

                // Create communication entry for the feedback
                if ($client) {
                    Communication::create([
                        'communicable_type' => Client::class,
                        'communicable_id' => $client->id,
                        'type' => Communication::TYPE_FEEDBACK,
                        'subject' => 'Customer Feedback',
                        'message' => $data['message'],
                        'user_id' => null, // Public submission
                        'scheduled_at' => now(),
                        'is_sent' => true,
                    ]);
                } elseif ($lead) {
                    Communication::create([
                        'communicable_type' => Lead::class,
                        'communicable_id' => $lead->id,
                        'type' => Communication::TYPE_FEEDBACK,
                        'subject' => 'Customer Feedback',
                        'message' => $data['message'],
                        'user_id' => null, // Public submission
                        'scheduled_at' => now(),
                        'is_sent' => true,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your feedback!',
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show feedback form (public view)
     */
    public function showForm()
    {
        return view('feedback.form');
    }
}

