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
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('type', ['direct', 'from_lead'])->default('direct')->after('is_active');
            $table->foreignId('converted_by')->nullable()->after('type')->constrained('users')->onDelete('set null');
            $table->timestamp('converted_at')->nullable()->after('converted_by');
            $table->string('lead_name')->nullable()->after('converted_at');
            $table->string('lead_company')->nullable()->after('lead_name');
            $table->string('lead_email')->nullable()->after('lead_company');
            $table->string('lead_phone')->nullable()->after('lead_email');
            $table->string('lead_avatar')->nullable()->after('lead_phone');

            $table->index('type');
            $table->index('converted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['converted_by']);
            $table->dropIndex(['type']);
            $table->dropIndex(['converted_by']);
            $table->dropColumn([
                'type',
                'converted_by',
                'converted_at',
                'lead_name',
                'lead_company',
                'lead_email',
                'lead_phone',
                'lead_avatar'
            ]);
        });
    }
};
