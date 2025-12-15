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

        $activities = Timesheet::where('staff_id', $staff->id)
            ->where('work_date', '>=', Carbon::now()->subMonths(3))
            ->orderBy('work_date', 'desc')
            ->orderBy('clock_in_time', 'desc')
            ->get()
            ->flatMap(function ($timesheet) {
                return [
                    [
                        'type' => 'clock_in',
                        'client_name' => $timesheet->client->company_name ?? 'Unknown',
                        'timestamp' => $timesheet->clock_in_time,
                        'work_date' => $timesheet->work_date,
                    ],
                    [
                        'type' => 'clock_out',
                        'client_name' => $timesheet->client->company_name ?? 'Unknown',
                        'timestamp' => $timesheet->clock_out_time,
                        'work_date' => $timesheet->work_date,
                    ],
                ];
            })
            ->filter(fn ($event) => $event['timestamp'])
            ->sortByDesc('timestamp')
            ->values();

        return view('staff.activity', compact('staff', 'activities'));
    }
}

