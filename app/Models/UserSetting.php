<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'email_notifications',
        'in_app_notifications',
        'sms_notifications',
        'push_notifications',
        'notify_new_leads',
        'notify_lead_updates',
        'notify_client_updates',
        'notify_staff_updates',
        'notify_invoice_updates',
        'notify_follow_up_tasks',
        'notify_communications',
        'notify_document_uploads',
        'receive_messages',
        'email_on_message',
        'notify_message_read',
        'message_frequency',
        'timezone',
        'date_format',
        'time_format',
        'language',
        'dark_mode',
        'items_per_page',
        'auto_assign_staff',
        'default_reminder_days',
        'show_completed_tasks',
        'show_archived_items',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'in_app_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'notify_new_leads' => 'boolean',
        'notify_lead_updates' => 'boolean',
        'notify_client_updates' => 'boolean',
        'notify_staff_updates' => 'boolean',
        'notify_invoice_updates' => 'boolean',
        'notify_follow_up_tasks' => 'boolean',
        'notify_communications' => 'boolean',
        'notify_document_uploads' => 'boolean',
        'receive_messages' => 'boolean',
        'email_on_message' => 'boolean',
        'notify_message_read' => 'boolean',
        'dark_mode' => 'boolean',
        'auto_assign_staff' => 'boolean',
        'show_completed_tasks' => 'boolean',
        'show_archived_items' => 'boolean',
        'items_per_page' => 'integer',
        'default_reminder_days' => 'integer',
    ];

    /**
     * Get the user that owns the settings
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create settings for a user
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            static::getDefaultSettings($userId)
        );
    }

    /**
     * Get default settings
     */
    protected static function getDefaultSettings(int $userId): array
    {
        return [
            'user_id' => $userId,
            'email_notifications' => true,
            'in_app_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'notify_new_leads' => true,
            'notify_lead_updates' => true,
            'notify_client_updates' => true,
            'notify_staff_updates' => true,
            'notify_invoice_updates' => true,
            'notify_follow_up_tasks' => true,
            'notify_communications' => true,
            'notify_document_uploads' => true,
            'receive_messages' => true,
            'email_on_message' => true,
            'notify_message_read' => false,
            'message_frequency' => 'immediate',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'language' => 'en',
            'dark_mode' => false,
            'items_per_page' => 15,
            'auto_assign_staff' => false,
            'default_reminder_days' => 7,
            'show_completed_tasks' => true,
            'show_archived_items' => false,
        ];
    }
}
