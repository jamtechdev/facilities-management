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
        $user = auth()->user();
        $role = $user->roles->first();

        // Only SuperAdmin role can access this dashboard (check by role, not permission)
        if (!$role || $role->name !== 'SuperAdmin') {
            abort(403, 'You do not have permission to access the SuperAdmin dashboard.');
        }

        $today = Carbon::today();

        $stats = [];

        // Only load stats if user has permission to view the card
        if ($user->can('view dashboard leads card')) {
            // Count all leads (excluding converted ones - they are deleted now)
            $stats['total_leads'] = Lead::count();
            // Count new leads by stage (not by date)
            $stats['new_leads'] = Lead::where('stage', Lead::STAGE_NEW_LEAD)->count();
            $stats['qualified_leads'] = Lead::where('stage', 'qualified')->count();
        }

        if ($user->can('view dashboard clients card')) {
            $stats['total_clients'] = Client::where('is_active', true)->count();
        }

        if ($user->can('view dashboard staff card')) {
            $stats['total_staff'] = Staff::where('is_active', true)->count();
        }

        if ($user->can('view dashboard revenue card')) {
            $stats['total_invoices'] = Invoice::count();
            $stats['revenue'] = Invoice::where('status', 'paid')->sum('total_amount');
        }

        // Total Users card commented out - stats calculation disabled
        // if ($user->can('view dashboard users card')) {
        //     $stats['total_users'] = User::count();
        //     // Count users with admin dashboard permission but not view roles permission
        //     $stats['total_admins'] = User::permission('view admin dashboard')
        //         ->whereDoesntHave('permissions', function($q) {
        //             $q->where('name', 'view roles');
        //         })->count();
        //     // Count users with view roles permission (SuperAdmin)
        //     $stats['total_superadmins'] = User::permission('view roles')->count();
        // }

        // Automated follow-up reminders - only load if user has permission
        $followUpReminders = collect();
        if ($user->can('view dashboard followup reminders card')) {
            $followUpReminders = FollowUpTask::where('is_completed', false)
                ->where('due_date', '<=', Carbon::now()->addDays(7))
                ->with(['lead.assignedStaff'])
                ->orderBy('due_date', 'asc')
                ->get();
        }

        // Recent activity - only load if user has permission
        $recentActivity = collect();
        if ($user->can('view dashboard recent activity card')) {
            $recentActivity = Lead::with('assignedStaff')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        // Lead stages breakdown - only if user can view lead stages graph
        $leadStages = [];
        if ($user->can('view dashboard lead stages graph')) {
            $leadStages = Lead::selectRaw('stage, COUNT(*) as count')
                ->groupBy('stage')
                ->pluck('count', 'stage')
                ->toArray();
        }

        // Leads over the last 7 days - only if user can view leads chart
        $leadsLast7Days = [];
        if ($user->can('view dashboard leads chart')) {
            $today = Carbon::today();

            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $dateStart = $date->copy()->startOfDay();
                $dateEnd = $date->copy()->endOfDay();

                $count = Lead::whereBetween('created_at', [$dateStart, $dateEnd])->count();

                $leadsLast7Days[] = [
                    'date' => $date->format('M d'),
                    'day' => $date->format('D'),
                    'full_date' => $date->format('Y-m-d'),
                    'count' => $count
                ];
            }
        }

        // Today's leads
        $todayLeads = Lead::whereDate('created_at', Carbon::today())->get();

        return view('superadmin.dashboard', compact(
            'stats',
            'leadStages',
            'leadsLast7Days',
            'followUpReminders',
            'recentActivity',
            'todayLeads'
        ));
    }
}
