<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Helpers\RouteHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display admin profile page
     */
    public function index()
    {
        $user = auth()->user();

        $documents = Document::where('documentable_type', User::class)
            ->where('documentable_id', $user->id)
            ->latest()
            ->get();

        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.profile', compact('user', 'documents'));
    }

    /**
     * Update admin profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
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

            if (isset($data['name'])) {
                $user->update(['name' => $data['name']]);
            }
            if (isset($data['email'])) {
                $user->update(['email' => $data['email']]);
            }

            // Update password if provided
            if (isset($data['password'])) {
                $user->update([
                    'password' => Hash::make($data['password'])
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => $user->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload document for admin
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $user = auth()->user();

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
            $path = $file->store('documents/users', 'public');

            $document = Document::create([
                'documentable_type' => User::class,
                'documentable_id' => $user->id,
                'name' => $request->name,
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'document_type' => $request->document_type ?? 'other',
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

    public function updateImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = auth()->user();

        if ($request->hasFile('profile_image')) {
            // Purani photo delete karein agar zaruri ho
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('profile_image')->store('profile-photos', 'public');
            $user->update(['avatar' => $path]);

            return response()->json([
                'success' => true,
                'image_url' => asset('storage/' . $path),
                'message' => 'Profile image updated!'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded']);
    }

    /**
     * Delete a document
     */
    public function deleteDocument(Document $document): JsonResponse
    {
        $user = auth()->user();

        if ($document->documentable_type !== User::class || $document->documentable_id !== $user->id) {
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
     * Download a document
     */
    public function downloadDocument(Document $document)
    {
        $user = auth()->user();

        if ($document->documentable_type !== User::class || $document->documentable_id !== $user->id) {
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
}
