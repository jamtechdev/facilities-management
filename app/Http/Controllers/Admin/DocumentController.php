<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Store a newly uploaded document
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'documentable_type' => 'required|string|in:App\Models\Lead,App\Models\Client,App\Models\Staff',
            'documentable_id' => 'required|integer',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240', // 10MB max
            'name' => 'required|string|max:255',
            'document_type' => 'nullable|string|in:agreement,proposal,signed_form,image,id,certificate,other',
            'description' => 'nullable|string',
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
            $path = $file->store('documents', 'public');

            $document = Document::create([
                'documentable_type' => $request->documentable_type,
                'documentable_id' => $request->documentable_id,
                'name' => $request->name,
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'document_type' => $request->document_type ?? Document::TYPE_OTHER,
                'description' => $request->description,
                'uploaded_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully.',
                'data' => $document->load('uploadedBy')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a document
     */
    public function destroy(Document $document): JsonResponse
    {
        try {
            // Delete file from storage
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
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document not found');
        }

        // Get the original file extension from the stored file
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

        // Format the download filename properly
        // Remove any existing extension from name and add the correct one
        $nameWithoutExt = pathinfo($document->name, PATHINFO_FILENAME);
        $downloadName = $nameWithoutExt . '.' . $extension;

        // Clean the filename (remove special characters that might cause issues)
        $downloadName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $downloadName);

        return Storage::disk('public')->download($document->file_path, $downloadName);
    }
}
