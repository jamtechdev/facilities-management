<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Models\Lead;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CommunicationController extends Controller
{
    /**
     * Store a new communication (call, email, meeting, note)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'communicable_type' => 'required|string|in:App\Models\Lead,App\Models\Client',
            'communicable_id' => 'required|integer',
            'type' => 'required|string|in:call,email,meeting,note',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'email_to' => 'nullable|email|required_if:type,email',
            'scheduled_at' => 'nullable|date',
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
            $data['user_id'] = auth()->id();
            $data['is_sent'] = false;

            // If email type, send the email
            if ($data['type'] === Communication::TYPE_EMAIL && isset($data['email_to'])) {
                $this->sendEmail($data);
                $data['is_sent'] = true;
                $data['email_from'] = auth()->user()->email;
            }

            $communication = Communication::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Communication logged successfully.',
                'data' => $communication->load('user')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log communication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send email through CRM
     */
    protected function sendEmail(array $data): void
    {
        try {
            Mail::raw($data['message'], function ($message) use ($data) {
                $message->to($data['email_to'])
                    ->subject($data['subject'] ?? 'Message from CRM')
                    ->from(auth()->user()->email, auth()->user()->name);
            });
        } catch (\Exception $e) {
            // Log error but don't fail the communication logging
            \Log::error('Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Update a communication
     */
    public function update(Request $request, Communication $communication): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|string|in:call,email,meeting,note',
            'subject' => 'nullable|string|max:255',
            'message' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $communication->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Communication updated successfully.',
                'data' => $communication->load('user')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update communication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a communication
     */
    public function destroy(Communication $communication): JsonResponse
    {
        try {
            $communication->delete();

            return response()->json([
                'success' => true,
                'message' => 'Communication deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete communication: ' . $e->getMessage()
            ], 500);
        }
    }
}

