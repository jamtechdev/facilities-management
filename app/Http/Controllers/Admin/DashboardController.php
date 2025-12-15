<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\FollowUpTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Redirect SuperAdmin to SuperAdmin dashboard
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('superadmin.dashboard');
        }

        $today = Carbon::today();
        $stats = [
            'total_leads' => Lead::count(),
            'new_leads' => Lead::whereDate('created_at', $today)->count(), // Today's new leads
            'qualified_leads' => Lead::where('stage', 'qualified')->count(),
            'total_clients' => Client::where('is_active', true)->count(),
            'total_staff' => Staff::where('is_active', true)->count(),
            'total_invoices' => Invoice::count(),
            'revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
        ];

        // Removed todayLeads and recentActivity as they're not needed in simplified dashboard

        // Lead stages breakdown
        $leadStages = Lead::selectRaw('stage, COUNT(*) as count')
            ->groupBy('stage')
            ->pluck('count', 'stage')
            ->toArray();

        // Leads over the last 7 days
        $leadsLast7Days = [];
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

        // Automated follow-up reminders (upcoming and overdue)
        $followUpReminders = FollowUpTask::where('is_completed', false)
            ->where('due_date', '<=', Carbon::now()->addDays(7)) // Next 7 days
            ->with(['lead.assignedStaff'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Today's leads
        $todayLeads = Lead::whereDate('created_at', Carbon::today())->get();

        // Recent activity (last 10 leads)
        $recentActivity = Lead::with('assignedStaff')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'leadStages',
            'leadsLast7Days',
            'followUpReminders',
            'todayLeads',
            'recentActivity'
        ));
    }
}
