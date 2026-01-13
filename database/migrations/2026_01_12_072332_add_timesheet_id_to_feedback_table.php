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
        Schema::table('feedback', function (Blueprint $table) {
            $table->foreignId('timesheet_id')->nullable()->after('client_id')->constrained('timesheets')->onDelete('set null');
            $table->index('timesheet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropForeign(['timesheet_id']);
            $table->dropIndex(['timesheet_id']);
            $table->dropColumn('timesheet_id');
        });
    }
};
