<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Helpers\RouteHelper;
use Illuminate\Http\Request;

class DocumentGalleryController extends Controller
{
    /**
     * Display the document gallery
     */
    public function index(Request $request)
    {
        $query = Document::with(['documentable', 'uploadedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by document type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('document_type', $request->type);
        }

        // Filter by entity type (Lead, Client, Staff, User)
        if ($request->has('entity_type') && $request->entity_type !== 'all') {
            $query->where('documentable_type', $request->entity_type);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $documents = $query->paginate(8);

        // Get counts for sidebar
        $totalDocuments = Document::count();
        $typeCounts = Document::selectRaw('document_type, count(*) as count')
            ->groupBy('document_type')
            ->pluck('count', 'document_type')
            ->toArray();

        $entityTypeCounts = Document::selectRaw('documentable_type, count(*) as count')
            ->groupBy('documentable_type')
            ->pluck('count', 'documentable_type')
            ->toArray();

        $viewPrefix = RouteHelper::getViewPrefix();

        return view($viewPrefix . '.documents.gallery', compact(
            'documents',
            'totalDocuments',
            'typeCounts',
            'entityTypeCounts'
        ));
    }
}

