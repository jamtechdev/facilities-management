<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\JobPhoto;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JobPhotoController extends Controller
{
    /**
     * Upload before/after photos
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'timesheet_id' => 'required|exists:timesheets,id',
            'photo_type' => 'required|string|in:before,after',
            'photos' => 'required|array|min:1|max:10',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png|max:5120', // 5MB max per photo
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

            $timesheet = Timesheet::findOrFail($request->timesheet_id);

            // Verify timesheet belongs to this staff member
            if ($timesheet->staff_id !== $staff->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this timesheet.'
                ], 403);
            }

            $uploadedPhotos = [];

            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('job-photos', 'public');

                $jobPhoto = JobPhoto::create([
                    'timesheet_id' => $timesheet->id,
                    'client_id' => $timesheet->client_id,
                    'staff_id' => $staff->id,
                    'photo_type' => $request->photo_type,
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'status' => JobPhoto::STATUS_PENDING,
                ]);

                $uploadedPhotos[] = $jobPhoto;
            }

            return response()->json([
                'success' => true,
                'message' => 'Photos uploaded successfully.',
                'data' => $uploadedPhotos
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a photo
     */
    public function destroy(JobPhoto $jobPhoto): JsonResponse
    {
        try {
            $user = auth()->user();
            $staff = $user->staff;

            // Only staff who uploaded it or admin can delete
            if ($jobPhoto->staff_id !== $staff->id && !$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this photo.'
                ], 403);
            }

            // Delete file from storage
            if (Storage::disk('public')->exists($jobPhoto->file_path)) {
                Storage::disk('public')->delete($jobPhoto->file_path);
            }

            $jobPhoto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo: ' . $e->getMessage()
            ], 500);
        }
    }
}

