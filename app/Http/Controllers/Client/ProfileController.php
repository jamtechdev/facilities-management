<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return redirect()->route('welcome')->with('error', 'Client profile not found.');
        }

        // Eager load projects if you have that relationship (to avoid previous error)
        // Agar aapke Client model mein projects() relation hai toh uncomment kar dena
        // $client->load('projects');

        $documents = $client->documents()->latest()->get();

        return view('client.profile', compact('client', 'user', 'documents'));
    }

    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_name'          => 'sometimes|string|max:255',
            'contact_person'        => 'sometimes|string|max:255',
            'email'                 => 'sometimes|email|max:255|unique:clients,email,' . $client->id,
            'phone'                 => 'nullable|string|max:20',
            'address'               => 'nullable|string',
            'city'                  => 'nullable|string|max:100',
            'postal_code'           => 'nullable|string|max:20',
            'agreed_weekly_hours'   => 'nullable|numeric|min:0|max:168',
            'agreed_monthly_hours'  => 'nullable|numeric|min:0|max:800',
            'billing_frequency'     => 'nullable|in:weekly,bi-weekly,monthly,quarterly',
            'notes'                 => 'nullable|string|max:1000',
            'password'              => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Update client fields
            $client->update([
                'company_name'         => $data['company_name'] ?? $client->company_name,
                'contact_person'       => $data['contact_person'] ?? $client->contact_person,
                'email'                => $data['email'] ?? $client->email,
                'phone'                => $data['phone'] ?? $client->phone,
                'address'              => $data['address'] ?? $client->address,
                'city'                 => $data['city'] ?? $client->city,
                'postal_code'          => $data['postal_code'] ?? $client->postal_code,
                'agreed_weekly_hours'  => $data['agreed_weekly_hours'] ?? $client->agreed_weekly_hours,
                'agreed_monthly_hours' => $data['agreed_monthly_hours'] ?? $client->agreed_monthly_hours,
                'billing_frequency'    => $data['billing_frequency'] ?? $client->billing_frequency,
                'notes'                => $data['notes'] ?? $client->notes,
            ]);

            // Update user email & password if changed
            if (isset($data['email'])) {
                $user->email = $data['email'];
                $user->save();
            }

            if (isset($data['password'])) {
                $user->password = Hash::make($data['password']);
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data'    => $client->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function uploadDocument(Request $request): JsonResponse
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client profile not found.'
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
            $path = $file->store('documents/client', 'public');

            $document = Document::create([
                'documentable_type' => Client::class,
                'documentable_id' => $client->id,
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
     * Delete a client document
     */
    public function destroyDocument(Document $document): JsonResponse
    {
        $user = auth()->user();
        $client = $user->client;

        if (!$client || $document->documentable_type !== Client::class || $document->documentable_id !== $client->id) {
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
        $client = $user->client;

        if (!$client || $document->documentable_type !== Client::class || $document->documentable_id !== $client->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->name);
    }
}
