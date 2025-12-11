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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // Polymorphic: can belong to Lead, Client, or Staff
            $table->string('name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // MIME type
            $table->integer('file_size')->nullable(); // in bytes
            $table->enum('document_type', ['agreement', 'proposal', 'signed_form', 'image', 'id', 'certificate', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Note: morphs() already creates index for documentable_type and documentable_id
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
