<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class FirebaseService
{
    protected $serverKey;
    protected $apiUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key');
    }

    /**
     * Send push notification to a single device
     */
    public function sendNotification(string $fcmToken, array $notification): bool
    {
        // Validate inputs
        if (empty($fcmToken)) {
            Log::warning('FCM token is empty');
            return false;
        }

        if (empty($this->serverKey)) {
            Log::warning('Firebase server key not configured. Please set FIREBASE_SERVER_KEY in .env');
            return false;
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $notification['title'] ?? 'Notification',
                    'body' => $notification['body'] ?? '',
                    'sound' => 'default',
                    'badge' => 1,
                ],
                'data' => $notification['data'] ?? [],
                'priority' => 'high',
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Check for Firebase-specific errors
                if (isset($responseData['failure']) && $responseData['failure'] > 0) {
                    Log::warning('Firebase notification partially failed', [
                        'fcm_token' => substr($fcmToken, 0, 20) . '...',
                        'response' => $responseData
                    ]);
                    return false;
                }

                Log::info('Firebase notification sent successfully', [
                    'fcm_token' => substr($fcmToken, 0, 20) . '...',
                    'success' => $responseData['success'] ?? 0
                ]);
                return true;
            } else {
                Log::error('Firebase notification HTTP error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Firebase connection error', [
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Firebase notification exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send push notification to multiple devices
     */
    public function sendToMultiple(array $fcmTokens, array $notification): array
    {
        $results = [];

        foreach ($fcmTokens as $token) {
            $results[$token] = $this->sendNotification($token, $notification);
        }

        return $results;
    }
}
