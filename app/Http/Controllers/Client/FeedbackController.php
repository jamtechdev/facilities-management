<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return redirect()->route('client.dashboard')->with('error', 'Client profile not found.');
        }

        // Get only feedback submitted by this client
        $feedbacks = Feedback::where('client_id', $client->id)
            ->latest()
            ->paginate(10);

        return view('client.feedback', compact('feedbacks', 'client'));
    }

    /**
     * Store new feedback from client
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client profile not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10',
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
            $feedback = Feedback::create([
                'email' => $client->email,
                'name' => $client->contact_person ?? $user->name,
                'company' => $client->company_name,
                'message' => $request->message,
                'rating' => $request->rating,
                'client_id' => $client->id,
                'is_processed' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!',
                'feedback' => $feedback
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback: ' . $e->getMessage()
            ], 500);
        }
    }
}
