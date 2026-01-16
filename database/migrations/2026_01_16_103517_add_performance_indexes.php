<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if an index exists on a table
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = DB::connection()->getDatabaseName();
        $result = DB::select(
            "SELECT COUNT(*) as count
             FROM information_schema.statistics
             WHERE table_schema = ?
             AND table_name = ?
             AND index_name = ?",
            [$connection, $table, $indexName]
        );
        return $result[0]->count > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table indexes
        if (!$this->hasIndex('users', 'users_created_at_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('created_at', 'users_created_at_index');
            });
        }
        if (!$this->hasIndex('users', 'users_email_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('email', 'users_email_index');
            });
        }

        // Leads table indexes
        if (!$this->hasIndex('leads', 'leads_created_at_index')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->index('created_at', 'leads_created_at_index');
            });
        }
        if (!$this->hasIndex('leads', 'leads_user_id_index')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->index('user_id', 'leads_user_id_index');
            });
        }
        if (!$this->hasIndex('leads', 'leads_assigned_staff_id_index')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->index('assigned_staff_id', 'leads_assigned_staff_id_index');
            });
        }

        // Clients table indexes
        if (!$this->hasIndex('clients', 'clients_created_at_index')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->index('created_at', 'clients_created_at_index');
            });
        }
        if (!$this->hasIndex('clients', 'clients_user_id_index')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->index('user_id', 'clients_user_id_index');
            });
        }

        // Staff table indexes
        if (!$this->hasIndex('staff', 'staff_created_at_index')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->index('created_at', 'staff_created_at_index');
            });
        }
        if (!$this->hasIndex('staff', 'staff_user_id_index')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->index('user_id', 'staff_user_id_index');
            });
        }

        // Invoices table indexes
        if (!$this->hasIndex('invoices', 'invoices_created_at_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index('created_at', 'invoices_created_at_index');
            });
        }
        if (!$this->hasIndex('invoices', 'invoices_client_id_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index('client_id', 'invoices_client_id_index');
            });
        }
        if (!$this->hasIndex('invoices', 'invoices_created_by_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index('created_by', 'invoices_created_by_index');
            });
        }

        // Timesheets table indexes
        if (!$this->hasIndex('timesheets', 'timesheets_created_at_index')) {
            Schema::table('timesheets', function (Blueprint $table) {
                $table->index('created_at', 'timesheets_created_at_index');
            });
        }
        if (!$this->hasIndex('timesheets', 'timesheets_staff_id_index')) {
            Schema::table('timesheets', function (Blueprint $table) {
                $table->index('staff_id', 'timesheets_staff_id_index');
            });
        }
        if (!$this->hasIndex('timesheets', 'timesheets_client_id_index')) {
            Schema::table('timesheets', function (Blueprint $table) {
                $table->index('client_id', 'timesheets_client_id_index');
            });
        }

        // Communications table indexes
        if (!$this->hasIndex('communications', 'communications_created_at_index')) {
            Schema::table('communications', function (Blueprint $table) {
                $table->index('created_at', 'communications_created_at_index');
            });
        }
        if (!$this->hasIndex('communications', 'communications_communicable_index')) {
            Schema::table('communications', function (Blueprint $table) {
                $table->index(['communicable_type', 'communicable_id'], 'communications_communicable_index');
            });
        }

        // Documents table indexes
        if (!$this->hasIndex('documents', 'documents_created_at_index')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->index('created_at', 'documents_created_at_index');
            });
        }
        if (!$this->hasIndex('documents', 'documents_documentable_index')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->index(['documentable_type', 'documentable_id'], 'documents_documentable_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table indexes
        if ($this->hasIndex('users', 'users_created_at_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_created_at_index');
            });
        }
        if ($this->hasIndex('users', 'users_email_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_email_index');
            });
        }

        // Leads table indexes
        if ($this->hasIndex('leads', 'leads_created_at_index')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropIndex('leads_created_at_index');
            });
        }
        if ($this->hasIndex('leads', 'leads_user_id_index')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropIndex('leads_user_id_index');
            });
        }
        if ($this->hasIndex('leads', 'leads_assigned_staff_id_index')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropIndex('leads_assigned_staff_id_index');
            });
        }

        // Clients table indexes
        if ($this->hasIndex('clients', 'clients_created_at_index')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropIndex('clients_created_at_index');
            });
        }
        if ($this->hasIndex('clients', 'clients_user_id_index')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropIndex('clients_user_id_index');
            });
        }

        // Staff table indexes
        if ($this->hasIndex('staff', 'staff_created_at_index')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropIndex('staff_created_at_index');
            });
        }
        if ($this->hasIndex('staff', 'staff_user_id_index')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropIndex('staff_user_id_index');
            });
        }

        // Invoices table indexes
        if ($this->hasIndex('invoices', 'invoices_created_at_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('invoices_created_at_index');
            });
        }
        if ($this->hasIndex('invoices', 'invoices_client_id_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('invoices_client_id_index');
            });
        }
        if ($this->hasIndex('invoices', 'invoices_created_by_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('invoices_created_by_index');
            });
        }

        // Timesheets table indexes
        if ($this->hasIndex('timesheets', 'timesheets_created_at_index')) {
            Schema::table('timesheets', function (Blueprint $table) {
                $table->dropIndex('timesheets_created_at_index');
            });
        }
        if ($this->hasIndex('timesheets', 'timesheets_staff_id_index')) {
            Schema::table('timesheets', function (Blueprint $table) {
                $table->dropIndex('timesheets_staff_id_index');
            });
        }
        if ($this->hasIndex('timesheets', 'timesheets_client_id_index')) {
            Schema::table('timesheets', function (Blueprint $table) {
                $table->dropIndex('timesheets_client_id_index');
            });
        }

        // Communications table indexes
        if ($this->hasIndex('communications', 'communications_created_at_index')) {
            Schema::table('communications', function (Blueprint $table) {
                $table->dropIndex('communications_created_at_index');
            });
        }
        if ($this->hasIndex('communications', 'communications_communicable_index')) {
            Schema::table('communications', function (Blueprint $table) {
                $table->dropIndex('communications_communicable_index');
            });
        }

        // Documents table indexes
        if ($this->hasIndex('documents', 'documents_created_at_index')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropIndex('documents_created_at_index');
            });
        }
        if ($this->hasIndex('documents', 'documents_documentable_index')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropIndex('documents_documentable_index');
            });
        }
    }

};
