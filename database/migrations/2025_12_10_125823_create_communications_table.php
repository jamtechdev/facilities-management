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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->morphs('communicable'); // Polymorphic: can belong to Lead or Client
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'feedback'])->default('note');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('email_to')->nullable();
            $table->string('email_from')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
            
            // Note: morphs() already creates index for communicable_type and communicable_id
            $table->index('type');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
