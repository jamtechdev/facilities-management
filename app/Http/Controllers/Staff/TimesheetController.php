<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;
use App\Models\Client;
use App\Models\JobPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimesheetController extends Controller
{
    /**
     * Display timesheet page with clock in/out functionality
     */
    public function index()
    {
        $user = auth()->user();
        $staff = $user->staff;

        if (!$staff) {
            return redirect()->route('welcome')->with('error', 'Staff profile not found.');
        }

        // Get assigned clients
        $assignedClients = $staff->clients()->wherePivot('is_active', true)->get();

        // Get today's timesheet entries
        $today = Carbon::today();
        $todayTimesheets = Timesheet::where('staff_id', $staff->id)
            ->where('work_date', $today)
            ->with('client')
            ->get();

        // Calculate today's total hours
        $todayHours = $todayTimesheets->sum('hours_worked');

        // Get weekly hours
        $weekStart = $today->copy()->startOfWeek();
        $weekHours = Timesheet::where('staff_id', $staff->id)
            ->whereBetween('work_date', [$weekStart, $today])
            ->sum('hours_worked');

        // Get monthly hours
        $monthStart = $today->copy()->startOfMonth();
        $monthHours = Timesheet::where('staff_id', $staff->id)
            ->whereBetween('work_date', [$monthStart, $today])
            ->sum('hours_worked');

        // Get days worked this month
        $daysWorked = Timesheet::where('staff_id', $staff->id)
            ->whereBetween('work_date', [$monthStart, $today])
            ->distinct('work_date')
            ->count('work_date');

        // Get time history (last 30 days)
        $timeHistory = Timesheet::where('staff_id', $staff->id)
            ->where('work_date', '>=', $today->copy()->subDays(30))
            ->with('client')
            ->orderBy('work_date', 'desc')
            ->orderBy('clock_in_time', 'desc')
            ->get();

        return view('staff.timesheet', compact(
            'staff',
            'assignedClients',
            'todayTimesheets',
            'todayHours',
            'weekHours',
            'monthHours',
            'daysWorked',
            'timeHistory'
        ));
    }

    /**
     * Clock in for a client
     */
    public function clockIn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            $staff = $user->staff;

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff profile not found.'
                ], 404);
            }

            // Check if staff is assigned to this client
            $isAssigned = $staff->clients()->where('clients.id', $request->client_id)
                ->wherePivot('is_active', true)
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this client.'
                ], 403);
            }

            // Check if there's already an active clock-in for today (any client)
            // Staff must clock out from previous session before clocking in again
            $today = Carbon::today();
            $existingTimesheet = Timesheet::where('staff_id', $staff->id)
                ->where('work_date', $today)
                ->whereNull('clock_out_time')
                ->first();

            if ($existingTimesheet) {
                $clientName = $existingTimesheet->client->company_name ?? 'a client';
                return response()->json([
                    'success' => false,
                    'message' => "You are already clocked in for {$clientName} today. Please clock out first before clocking in again.",
                    'has_active_session' => true,
                    'active_timesheet_id' => $existingTimesheet->id
                ], 400);
            }

            $timesheet = Timesheet::create([
                'staff_id' => $staff->id,
                'client_id' => $request->client_id,
                'work_date' => $today,
                'clock_in_time' => now(),
                'hours_worked' => 0,
                'payable_hours' => 0,
                'is_approved' => false,
                'status' => Timesheet::STATUS_PENDING, // Status pending when clock in
            ]);

            $photo = $request->file('photo');
            $path = $photo->store('job-photos', 'public');

            JobPhoto::create([
                'timesheet_id' => $timesheet->id,
                'client_id' => $timesheet->client_id,
                'staff_id' => $staff->id,
                'photo_type' => JobPhoto::TYPE_BEFORE,
                'file_path' => $path,
                'file_name' => $photo->getClientOriginalName(),
                'file_size' => $photo->getSize(),
                'status' => JobPhoto::STATUS_PENDING,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Clocked in successfully.',
                'data' => $timesheet->load('client')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clock in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clock out for a client
     */
    public function clockOut(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'timesheet_id' => 'required|exists:timesheets,id',
            'notes' => 'nullable|string',
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            $staff = $user->staff;

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff profile not found.'
                ], 404);
            }

            $timesheet = Timesheet::where('id', $request->timesheet_id)
                ->where('staff_id', $staff->id)
                ->whereNull('clock_out_time')
                ->firstOrFail();

            $clockOutTime = now();
            $hoursWorked = $timesheet->clock_in_time->diffInMinutes($clockOutTime) / 60;

            // Calculate payable hours (if works more than assigned, only assigned hours are paid)
            $client = $timesheet->client;
            $assignment = $staff->clients()->where('clients.id', $client->id)
                ->wherePivot('is_active', true)
                ->first();

            $assignedHours = $assignment?->pivot->assigned_weekly_hours ?? $staff->assigned_weekly_hours ?? 0;
            $payableHours = min($hoursWorked, $assignedHours);

            $timesheet->update([
                'clock_out_time' => $clockOutTime,
                'hours_worked' => round($hoursWorked, 2),
                'payable_hours' => round($payableHours, 2),
                'notes' => $request->notes,
                'status' => Timesheet::STATUS_COMPLETED, // Status completed when clock out
            ]);

            $photo = $request->file('photo');
            $path = $photo->store('job-photos', 'public');

            JobPhoto::create([
                'timesheet_id' => $timesheet->id,
                'client_id' => $timesheet->client_id,
                'staff_id' => $staff->id,
                'photo_type' => JobPhoto::TYPE_AFTER,
                'file_path' => $path,
                'file_name' => $photo->getClientOriginalName(),
                'file_size' => $photo->getSize(),
                'status' => JobPhoto::STATUS_PENDING,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Clocked out successfully.',
                'data' => $timesheet->load('client')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clock out: ' . $e->getMessage()
            ], 500);
        }
    }
}
