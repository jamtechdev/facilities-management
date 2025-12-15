<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class JobPhotoController extends Controller
{
    /**
     * Approve a job photo
     */
    public function approve(Request $request, JobPhoto $jobPhoto): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $jobPhoto->update([
                'status' => JobPhoto::STATUS_APPROVED,
                'admin_notes' => $request->admin_notes,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo approved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a job photo
     */
    public function reject(Request $request, JobPhoto $jobPhoto): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $jobPhoto->update([
                'status' => JobPhoto::STATUS_REJECTED,
                'admin_notes' => $request->admin_notes,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo rejected successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a job photo
     */
    public function download(JobPhoto $jobPhoto)
    {
        if (!\Storage::disk('public')->exists($jobPhoto->file_path)) {
            abort(404, 'Photo not found');
        }

        return \Storage::disk('public')->download($jobPhoto->file_path, $jobPhoto->file_name);
    }
}

