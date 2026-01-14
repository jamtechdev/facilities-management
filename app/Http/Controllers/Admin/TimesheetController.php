<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TimesheetController extends Controller
{
    /**
     * Update timesheet status
     */
    public function updateStatus(Request $request, Timesheet $timesheet): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,approved',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $status = $request->status;

            $updateData = [
                'status' => $status,
            ];

            // If approving, also set is_approved and approved_by
            if ($status === Timesheet::STATUS_APPROVED) {
                $updateData['is_approved'] = true;
                $updateData['approved_by'] = auth()->id();
                $updateData['approved_at'] = now();
            } elseif ($status === Timesheet::STATUS_COMPLETED) {
                // If changing to completed, keep is_approved as is but update status
                // Don't change is_approved if it was already approved
            } else {
                // If changing to pending, reset approval
                $updateData['is_approved'] = false;
                $updateData['approved_by'] = null;
                $updateData['approved_at'] = null;
            }

            $timesheet->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Timesheet status updated successfully.',
                'data' => [
                    'status' => $timesheet->status,
                    'is_approved' => $timesheet->is_approved,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update timesheet status: ' . $e->getMessage()
            ], 500);
        }
    }
}
