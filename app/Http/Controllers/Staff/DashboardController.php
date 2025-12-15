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

        // Get today's tasks
        $todayTasks = Timesheet::where('staff_id', $staff->id)
            ->where('work_date', $today)
            ->with('client')
            ->get();

        return view('staff.dashboard', compact(
            'staff',
            'todayHours',
            'weekHours',
            'monthHours',
            'assignedCompanies',
            'todayTasks'
        ));
    }
}
