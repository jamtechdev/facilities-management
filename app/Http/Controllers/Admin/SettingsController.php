<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSetting;
use App\Helpers\RouteHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);

        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.settings', compact('settings'));
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request): JsonResponse
    {
        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);

        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'in_app_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'notify_new_leads' => 'sometimes|boolean',
            'notify_lead_updates' => 'sometimes|boolean',
            'notify_client_updates' => 'sometimes|boolean',
            'notify_staff_updates' => 'sometimes|boolean',
            'notify_invoice_updates' => 'sometimes|boolean',
            'notify_follow_up_tasks' => 'sometimes|boolean',
            'notify_communications' => 'sometimes|boolean',
            'notify_document_uploads' => 'sometimes|boolean',
        ]);

        // Convert checkbox values to boolean
        $data = [];
        foreach ($validated as $key => $value) {
            $data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        $settings->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully.',
            'data' => $settings->fresh()
        ]);
    }

    /**
     * Update message settings
     */
    public function updateMessages(Request $request): JsonResponse
    {
        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);

        $validated = $request->validate([
            'receive_messages' => 'sometimes|boolean',
            'email_on_message' => 'sometimes|boolean',
            'notify_message_read' => 'sometimes|boolean',
            'message_frequency' => 'sometimes|in:immediate,daily,weekly',
        ]);

        // Convert checkbox values to boolean
        $data = [];
        foreach ($validated as $key => $value) {
            if (in_array($key, ['receive_messages', 'email_on_message', 'notify_message_read'])) {
                $data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } else {
                $data[$key] = $value;
            }
        }

        $settings->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Message settings updated successfully.',
            'data' => $settings->fresh()
        ]);
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request): JsonResponse
    {
        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);

        $validated = $request->validate([
            'timezone' => 'sometimes|string|max:50',
            'date_format' => 'sometimes|string|max:20',
            'time_format' => 'sometimes|string|max:20',
            'language' => 'sometimes|string|max:10',
            'dark_mode' => 'sometimes|boolean',
            'items_per_page' => 'sometimes|integer|min:5|max:100',
        ]);

        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'General settings updated successfully.',
            'data' => $settings->fresh()
        ]);
    }

    /**
     * Update project settings
     */
    public function updateProject(Request $request): JsonResponse
    {
        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);

        $validated = $request->validate([
            'auto_assign_staff' => 'sometimes|boolean',
            'default_reminder_days' => 'sometimes|integer|min:1|max:30',
            'show_completed_tasks' => 'sometimes|boolean',
            'show_archived_items' => 'sometimes|boolean',
        ]);

        // Convert checkbox values to boolean
        $data = [];
        foreach ($validated as $key => $value) {
            if (in_array($key, ['auto_assign_staff', 'show_completed_tasks', 'show_archived_items'])) {
                $data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } else {
                $data[$key] = $value;
            }
        }

        $settings->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Project settings updated successfully.',
            'data' => $settings->fresh()
        ]);
    }

    /**
     * Update all settings at once
     */
    public function updateAll(Request $request): JsonResponse
    {
        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);

        $validated = $request->validate([
            // Notification settings
            'email_notifications' => 'sometimes|boolean',
            'in_app_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'notify_new_leads' => 'sometimes|boolean',
            'notify_lead_updates' => 'sometimes|boolean',
            'notify_client_updates' => 'sometimes|boolean',
            'notify_staff_updates' => 'sometimes|boolean',
            'notify_invoice_updates' => 'sometimes|boolean',
            'notify_follow_up_tasks' => 'sometimes|boolean',
            'notify_communications' => 'sometimes|boolean',
            'notify_document_uploads' => 'sometimes|boolean',
            // Message settings
            'receive_messages' => 'sometimes|boolean',
            'email_on_message' => 'sometimes|boolean',
            'notify_message_read' => 'sometimes|boolean',
            'message_frequency' => 'sometimes|in:immediate,daily,weekly',
            // General settings
            'timezone' => 'sometimes|string|max:50',
            'date_format' => 'sometimes|string|max:20',
            'time_format' => 'sometimes|string|max:20',
            'language' => 'sometimes|string|max:10',
            'dark_mode' => 'sometimes|boolean',
            'items_per_page' => 'sometimes|integer|min:5|max:100',
            // Project settings
            'auto_assign_staff' => 'sometimes|boolean',
            'default_reminder_days' => 'sometimes|integer|min:1|max:30',
            'show_completed_tasks' => 'sometimes|boolean',
            'show_archived_items' => 'sometimes|boolean',
        ]);

        // Convert checkbox values to boolean
        $data = [];
        foreach ($validated as $key => $value) {
            $booleanFields = [
                'email_notifications', 'in_app_notifications', 'sms_notifications',
                'notify_new_leads', 'notify_lead_updates', 'notify_client_updates',
                'notify_staff_updates', 'notify_invoice_updates', 'notify_follow_up_tasks',
                'notify_communications', 'notify_document_uploads',
                'receive_messages', 'email_on_message', 'notify_message_read',
                'dark_mode', 'auto_assign_staff', 'show_completed_tasks', 'show_archived_items'
            ];

            if (in_array($key, $booleanFields)) {
                $data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } else {
                $data[$key] = $value;
            }
        }

        $settings->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully.',
            'data' => $settings->fresh()
        ]);
    }
}
