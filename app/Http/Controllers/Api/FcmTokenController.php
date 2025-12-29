<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    /**
     * Store FCM token for authenticated user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'fcm_token' => 'required|string|max:500',
            ]);

            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user->fcm_token = $validated['fcm_token'];
            $user->save();

            \Illuminate\Support\Facades\Log::info('FCM token saved', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token saved successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to save FCM token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save FCM token. Please try again later.'
            ], 500);
        }
    }
}
