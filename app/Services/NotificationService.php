<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use App\Mail\UserRegistrationMail;
use App\Mail\NewUserNotificationMail;
use App\Mail\NewLeadNotificationMail;
use App\Mail\LeadWelcomeMail;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send registration email to new user with credentials
     */
    public function sendUserRegistrationEmail(User $user, string $password, string $role): void
    {
        try {
            Mail::to($user->email)->send(new UserRegistrationMail($user, $password, $role));
            Log::info('Registration email sent to user', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('Failed to send registration email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification to admins/superadmins about new user registration
     */
    public function notifyAdminsNewUser(User $newUser, string $role): void
    {
        try {
            // Get all admins and superadmins
            $admins = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['Admin', 'SuperAdmin']);
            })->get();

            foreach ($admins as $admin) {
                // Check if admin has email notifications enabled
                $settings = UserSetting::getOrCreateForUser($admin->id);

                if ($settings->email_notifications && $settings->notify_staff_updates) {
                    try {
                        Mail::to($admin->email)->send(new NewUserNotificationMail($newUser, $role));
                        Log::info('New user notification sent to admin', [
                            'admin_id' => $admin->id,
                            'new_user_id' => $newUser->id
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send new user notification to admin', [
                            'admin_id' => $admin->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Send push notification if enabled
                if ($settings->in_app_notifications && $settings->notify_staff_updates) {
                    $this->sendPushNotification($admin, [
                        'title' => 'New ' . ucfirst($role) . ' Registration',
                        'body' => $newUser->name . ' has registered as ' . ucfirst($role),
                        'type' => 'new_user',
                        'data' => [
                            'user_id' => $newUser->id,
                            'role' => $role,
                        ]
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about new user', [
                'new_user_id' => $newUser->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send welcome email to lead and FCM push notifications to admins/superadmins
     * - Sends email to lead contact (with credentials)
     * - Sends FCM push notifications to all admins/superadmins (no email notifications)
     */
    public function notifyAdminsNewLead(Lead $lead, $user = null, $password = null): void
    {
        try {
            // 1. Always send welcome email to the lead contact (with credentials)
            try {
                Mail::to($lead->email)->send(new LeadWelcomeMail($lead, $user, $password));
                Log::info('Welcome email with credentials sent to lead', [
                    'lead_id' => $lead->id,
                    'lead_email' => $lead->email,
                    'user_id' => $user ? $user->id : null
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email to lead', [
                    'lead_id' => $lead->id,
                    'lead_email' => $lead->email,
                    'error' => $e->getMessage()
                ]);
            }

            // 2. Send FCM push notifications to all admins and superadmins
            try {
                $admins = User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['Admin', 'SuperAdmin']);
                })->get();

                foreach ($admins as $admin) {
                    // Check if admin has push notifications enabled
                    $settings = UserSetting::getOrCreateForUser($admin->id);

                    if ($settings->push_notifications && $settings->notify_new_leads) {
                        $this->sendPushNotification($admin, [
                            'title' => 'New Lead Created',
                            'body' => 'A new lead "' . $lead->name . '" has been created',
                            'type' => 'new_lead',
                            'data' => [
                                'lead_id' => $lead->id,
                                'lead_name' => $lead->name,
                            ]
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send FCM push notifications for new lead', [
                    'lead_id' => $lead->id,
                    'error' => $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about new lead', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send push notification via Firebase
     */
    public function sendPushNotification(User $user, array $notification): void
    {
        try {
            // Check if user has push notifications enabled
            $settings = UserSetting::getOrCreateForUser($user->id);

            if (!$settings->push_notifications) {
                return;
            }

            // Get user's FCM token
            $fcmToken = $user->fcm_token ?? null;

            if (!$fcmToken) {
                Log::info('User does not have FCM token', ['user_id' => $user->id]);
                return;
            }

            // Use Firebase service to send notification
            $firebaseService = app(\App\Services\FirebaseService::class);
            $firebaseService->sendNotification($fcmToken, $notification);

            Log::info('Push notification sent', [
                'user_id' => $user->id,
                'type' => $notification['type'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send push notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
