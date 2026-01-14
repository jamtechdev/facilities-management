<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timesheet;
use App\Models\Client;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ensure user has staff dashboard permission
        if (!$user->can('view staff dashboard')) {
            abort(403, 'You do not have permission to access the staff dashboard.');
        }

        // Prevent other role users from accessing staff dashboard
        if ($user->can('view admin dashboard') || $user->can('view client dashboard') || $user->can('view lead dashboard')) {
            abort(403, 'You must use your designated dashboard.');
        }

        $staff = $user->staff;

        if (!$staff) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Staff profile not found. Please contact administrator.');
        }

        // Get today's stats
        $today = Carbon::today();
        $todayHours = Timesheet::where('staff_id', $staff->id)
            ->where('work_date', $today)
            ->sum('hours_worked');

        // Get weekly stats
        $weekStart = $today->copy()->startOfWeek();
        $weekHours = Timesheet::where('staff_id', $staff->id)
            ->whereBetween('work_date', [$weekStart, $today])
            ->sum('hours_worked');

        // Get monthly stats
        $monthStart = $today->copy()->startOfMonth();
        $monthHours = Timesheet::where('staff_id', $staff->id)
            ->whereBetween('work_date', [$monthStart, $today])
            ->sum('hours_worked');

        // Get assigned companies count
        $assignedCompanies = $staff->clients()->wherePivot('is_active', true)->count();

        // Get days worked this month
        $daysWorked = Timesheet::where('staff_id', $staff->id)
            ->whereBetween('work_date', [$monthStart, $today])
            ->distinct('work_date')
            ->count('work_date');

        // Get today's tasks
        $todayTasks = Timesheet::where('staff_id', $staff->id)
            ->where('work_date', $today)
            ->with('client')
            ->get();

        // Get recent activity (last 10 timesheet entries)
        $recentActivity = Timesheet::where('staff_id', $staff->id)
            ->with('client')
            ->orderBy('work_date', 'desc')
            ->orderBy('clock_in_time', 'desc')
            ->take(10)
            ->get();

        return view('staff.dashboard', compact(
            'staff',
            'todayHours',
            'weekHours',
            'monthHours',
            'assignedCompanies',
            'daysWorked',
            'todayTasks',
            'recentActivity'
        ));
    }
}
