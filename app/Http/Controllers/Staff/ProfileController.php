<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display staff profile page
     */
    public function index()
    {
        $user = auth()->user();
        $staff = $user->staff;

        if (!$staff) {
            return redirect()->route('welcome')->with('error', 'Staff profile not found.');
        }

        $documents = $staff->documents()->latest()->get();

        return view('staff.profile', compact('staff', 'user', 'documents'));
    }

    /**
     * Update staff profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();
        $staff = $user->staff;

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff profile not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'sometimes|email|max:255|unique:staff,email,' . $staff->id,
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Update staff profile
            if (isset($data['name'])) {
                $staff->update(['name' => $data['name']]);
            }
            if (isset($data['mobile'])) {
                $staff->update(['mobile' => $data['mobile']]);
            }
            if (isset($data['email'])) {
                $staff->update(['email' => $data['email']]);
                $user->update(['email' => $data['email']]);
            }
            if (isset($data['address'])) {
                $staff->update(['address' => $data['address']]);
            }

            // Update user password if provided
            if (isset($data['password'])) {
                $user->update([
                    'password' => Hash::make($data['password'])
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => $staff->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload compliance documents for staff
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $user = auth()->user();
        $staff = $user->staff;

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff profile not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'document_type' => 'nullable|string|in:id,certificate,agreement,proposal,signed_form,image,other',
            'description' => 'nullable|string|max:500',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('document');
            $path = $file->store('documents/staff', 'public');

            $document = Document::create([
                'documentable_type' => Staff::class,
                'documentable_id' => $staff->id,
                'name' => $request->name,
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'document_type' => $request->document_type ?? Document::TYPE_OTHER,
                'description' => $request->description,
                'uploaded_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully.',
                'data' => $document
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a staff document
     */
    public function deleteDocument(Document $document): JsonResponse
    {
        $user = auth()->user();
        $staff = $user->staff;

        if (!$staff || $document->documentable_type !== Staff::class || $document->documentable_id !== $staff->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        try {
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a staff document
     */
    public function downloadDocument(Document $document)
    {
        $user = auth()->user();
        $staff = $user->staff;

        if (!$staff || $document->documentable_type !== Staff::class || $document->documentable_id !== $staff->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document not found.');
        }

        // Get the original file extension from the stored file
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

        // Format the download filename properly
        $nameWithoutExt = pathinfo($document->name, PATHINFO_FILENAME);
        $downloadName = $nameWithoutExt . '.' . $extension;

        // Clean the filename
        $downloadName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $downloadName);

        return Storage::disk('public')->download($document->file_path, $downloadName);
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('profile-photos', 'public');

            $user->update(['avatar' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Profile image updated successfully',
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }
}
