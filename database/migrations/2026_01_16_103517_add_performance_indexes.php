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
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->index('created_at', 'users_created_at_index');
            } catch (\Exception $e) {
                // Index may already exist
            }
            try {
                $table->index('email', 'users_email_index');
            } catch (\Exception $e) {
                // Index may already exist
            }
        });

        // Leads table indexes
        Schema::table('leads', function (Blueprint $table) {
            try {
                $table->index('created_at', 'leads_created_at_index');
            } catch (\Exception $e) {}
            try {
                $table->index('user_id', 'leads_user_id_index');
            } catch (\Exception $e) {}
            try {
                $table->index('assigned_staff_id', 'leads_assigned_staff_id_index');
            } catch (\Exception $e) {}
        });

        // Clients table indexes
        Schema::table('clients', function (Blueprint $table) {
            try {
                $table->index('created_at', 'clients_created_at_index');
            } catch (\Exception $e) {}
            try {
                $table->index('user_id', 'clients_user_id_index');
            } catch (\Exception $e) {}
        });

        // Staff table indexes
        Schema::table('staff', function (Blueprint $table) {
            try {
                $table->index('created_at', 'staff_created_at_index');
            } catch (\Exception $e) {}
            try {
                $table->index('user_id', 'staff_user_id_index');
            } catch (\Exception $e) {}
        });

        // Invoices table indexes
        Schema::table('invoices', function (Blueprint $table) {
            try {
                $table->index('created_at', 'invoices_created_at_index');
            } catch (\Exception $e) {}
            try {
                $table->index('client_id', 'invoices_client_id_index');
            } catch (\Exception $e) {}
            try {
                $table->index('created_by', 'invoices_created_by_index');
            } catch (\Exception $e) {}
        });

        // Timesheets table indexes
        Schema::table('timesheets', function (Blueprint $table) {
            try {
                $table->index('created_at', 'timesheets_created_at_index');
            } catch (\Exception $e) {}
            try {
                $table->index('staff_id', 'timesheets_staff_id_index');
            } catch (\Exception $e) {}
            try {
                $table->index('client_id', 'timesheets_client_id_index');
            } catch (\Exception $e) {}
        });

        // Communications table indexes
        Schema::table('communications', function (Blueprint $table) {
            try {
                $table->index('created_at', 'communications_created_at_index');
            } catch (\Exception $e) {}
            try {
                $table->index(['communicable_type', 'communicable_id'], 'communications_communicable_index');
            } catch (\Exception $e) {}
        });

        // Documents table indexes
        Schema::table('documents', function (Blueprint $table) {
            try {
                $table->index('created_at', 'documents_created_at_index');
            } catch (\Exception $e) {}
            try {
                $table->index(['documentable_type', 'documentable_id'], 'documents_documentable_index');
            } catch (\Exception $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_created_at_index');
            $table->dropIndex('users_email_index');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_created_at_index');
            $table->dropIndex('leads_user_id_index');
            $table->dropIndex('leads_assigned_staff_id_index');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_created_at_index');
            $table->dropIndex('clients_user_id_index');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex('staff_created_at_index');
            $table->dropIndex('staff_user_id_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_created_at_index');
            $table->dropIndex('invoices_client_id_index');
            $table->dropIndex('invoices_created_by_index');
        });

        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropIndex('timesheets_created_at_index');
            $table->dropIndex('timesheets_staff_id_index');
            $table->dropIndex('timesheets_client_id_index');
        });

        Schema::table('communications', function (Blueprint $table) {
            $table->dropIndex('communications_created_at_index');
            $table->dropIndex('communications_communicable_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_created_at_index');
            $table->dropIndex('documents_documentable_index');
        });
    }

};
