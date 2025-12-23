<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Notification Settings
            $table->boolean('email_notifications')->default(true);
            $table->boolean('in_app_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('notify_new_leads')->default(true);
            $table->boolean('notify_lead_updates')->default(true);
            $table->boolean('notify_client_updates')->default(true);
            $table->boolean('notify_staff_updates')->default(true);
            $table->boolean('notify_invoice_updates')->default(true);
            $table->boolean('notify_follow_up_tasks')->default(true);
            $table->boolean('notify_communications')->default(true);
            $table->boolean('notify_document_uploads')->default(true);
            
            // Message Settings
            $table->boolean('receive_messages')->default(true);
            $table->boolean('email_on_message')->default(true);
            $table->boolean('notify_message_read')->default(false);
            $table->string('message_frequency')->default('immediate'); // immediate, daily, weekly
            
            // General Settings
            $table->string('timezone')->default('UTC');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i');
            $table->string('language')->default('en');
            $table->boolean('dark_mode')->default(false);
            $table->integer('items_per_page')->default(15);
            
            // Project Specific Settings
            $table->boolean('auto_assign_staff')->default(false);
            $table->integer('default_reminder_days')->default(7);
            $table->boolean('show_completed_tasks')->default(true);
            $table->boolean('show_archived_items')->default(false);
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
