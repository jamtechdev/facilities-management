<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;
use Illuminate\Support\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display chronological activity log for the staff user.
     */
    public function index()
    {
        $user = auth()->user();
        $staff = $user->staff;

        if (!$staff) {
            return redirect()->route('welcome')->with('error', 'Staff profile not found.');
        }

        // Get timesheets with all related data
        $timesheets = Timesheet::where('staff_id', $staff->id)
            ->where('work_date', '>=', Carbon::now()->subMonths(3))
            ->with(['client', 'jobPhotos'])
            ->orderBy('work_date', 'desc')
            ->orderBy('clock_in_time', 'desc')
            ->get();

        return view('staff.activity', compact('staff', 'timesheets'));
    }
}
