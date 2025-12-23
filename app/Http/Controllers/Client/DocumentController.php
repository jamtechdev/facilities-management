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
            return response()->download($filePath, $document->name);
        }

        abort(404, 'File not found.');
    }
}
