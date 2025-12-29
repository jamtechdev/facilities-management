<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Document; // ✅ Correct import
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function documents()
    {
        $documents = auth()->user()->client->documents()->latest()->get();
        return view('client.documents', compact('documents'));
    }

    public function download(Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);

        if (file_exists($filePath)) {
            // Get the original file extension from the stored file
            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

            // Format the download filename properly
            $nameWithoutExt = pathinfo($document->name, PATHINFO_FILENAME);
            $downloadName = $nameWithoutExt . '.' . $extension;

            // Clean the filename
            $downloadName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $downloadName);

            return response()->download($filePath, $downloadName);
        }

        abort(404, 'File not found.');
    }
}
