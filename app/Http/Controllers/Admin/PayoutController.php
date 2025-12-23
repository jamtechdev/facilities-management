<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PayoutController extends Controller
{
    /**
     * Display payout calculation page
     */
    public function index()
    {
        $staff = Staff::where('is_active', true)->get();
        return view('superadmin.payouts.index', compact('staff'));
    }

    /**
     * Calculate payout for staff
     */
    public function calculate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $staff = Staff::findOrFail($request->staff_id);
            
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            // Get approved timesheets in the date range
            $timesheets = Timesheet::where('staff_id', $staff->id)
                ->whereBetween('work_date', [$startDate, $endDate])
                ->where('is_approved', true)
                ->get();

            // Calculate total payable hours
            $totalPayableHours = $timesheets->sum('payable_hours');
            
            // Calculate payout = payable hours × hourly rate
            $payout = $totalPayableHours * $staff->hourly_rate;

            // Get assigned hours for comparison
            $assignedHours = $staff->assigned_weekly_hours ?? 0;
            $totalWorkedHours = $timesheets->sum('hours_worked');

            $data = [
                'staff' => $staff,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_worked_hours' => round($totalWorkedHours, 2),
                'total_payable_hours' => round($totalPayableHours, 2),
                'assigned_hours' => $assignedHours,
                'hourly_rate' => $staff->hourly_rate,
                'payout' => round($payout, 2),
                'timesheets' => $timesheets,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate payout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download payout report as PDF
     */
    public function downloadPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $staff = Staff::findOrFail($request->staff_id);
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $timesheets = Timesheet::where('staff_id', $staff->id)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('is_approved', true)
            ->with('client')
            ->get();

        $totalPayableHours = $timesheets->sum('payable_hours');
        $payout = $totalPayableHours * $staff->hourly_rate;

        $data = [
            'staff' => $staff,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_payable_hours' => round($totalPayableHours, 2),
            'hourly_rate' => $staff->hourly_rate,
            'payout' => round($payout, 2),
            'timesheets' => $timesheets,
        ];

        $pdf = Pdf::loadView('superadmin.payouts.pdf', $data);
        
        $filename = 'payout-' . $staff->name . '-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}

