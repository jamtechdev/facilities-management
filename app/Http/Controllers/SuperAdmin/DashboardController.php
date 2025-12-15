<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\User;
use App\Models\FollowUpTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the SuperAdmin dashboard.
     */
    public function index()
    {
        $today = Carbon::today();
        $stats = [
            'total_leads' => Lead::count(),
            'new_leads' => Lead::whereDate('created_at', $today)->count(),
            'qualified_leads' => Lead::where('stage', 'qualified')->count(),
            'total_clients' => Client::where('is_active', true)->count(),
            'total_staff' => Staff::where('is_active', true)->count(),
            'total_invoices' => Invoice::count(),
            'revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'total_users' => User::count(),
            'total_admins' => User::role('Admin')->count(),
            'total_superadmins' => User::role('SuperAdmin')->count(),
        ];

        // Automated follow-up reminders
        $followUpReminders = FollowUpTask::where('is_completed', false)
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->with(['lead.assignedStaff'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Recent activity (last 10 leads)
        $recentActivity = Lead::with('assignedStaff')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact(
            'stats',
            'followUpReminders',
            'recentActivity'
        ));
    }
}

