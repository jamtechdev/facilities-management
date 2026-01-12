@extends('layouts.app')

@section('title', 'Document Gallery')

@push('styles')
    @vite(['resources/css/profile.css', 'resources/css/document-gallery.css'])
@endpush

@section('content')
<div class="container-fluid">
    <!-- Document Gallery Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <i class="bi bi-folder2-open icon-2-5rem"></i>
            </div>
            <div class="profile-info flex-grow-1">
                <h1>Document Gallery</h1>
                <p>Browse and manage all documents</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 sidebar-gallery">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filters
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Search Documents</label>
                        <input type="text"
                               class="form-control"
                               id="searchInput"
                               placeholder="Search by name..."
                               value="{{ request('search') }}">
                    </div>

                    <!-- Document Type Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Document Type</label>
                        <div class="list-group list-group-flush">
                            <a href="{{ request()->fullUrlWithQuery(['type' => 'all', 'page' => 1]) }}"
                               class="list-group-item list-group-item-action {{ !request('type') || request('type') == 'all' ? 'active' : '' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>All Types</span>
                                    <span class="badge bg-secondary">{{ $totalDocuments }}</span>
                                </div>
                            </a>
                            @foreach(['agreement', 'proposal', 'signed_form', 'image', 'id', 'certificate', 'other'] as $type)
                                @if(isset($typeCounts[$type]) && $typeCounts[$type] > 0)
                                    <a href="{{ request()->fullUrlWithQuery(['type' => $type, 'page' => 1]) }}"
                                       class="list-group-item list-group-item-action {{ request('type') == $type ? 'active' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                            <span class="badge bg-primary">{{ $typeCounts[$type] }}</span>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Entity Type Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Entity Type</label>
                        <div class="list-group list-group-flush">
                            <a href="{{ request()->fullUrlWithQuery(['entity_type' => 'all', 'page' => 1]) }}"
                               class="list-group-item list-group-item-action {{ !request('entity_type') || request('entity_type') == 'all' ? 'active' : '' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>All Entities</span>
                                    <span class="badge bg-secondary">{{ $totalDocuments }}</span>
                                </div>
                            </a>
                            @php
                                $entityLabels = [
                                    'App\Models\Lead' => 'Leads',
                                    'App\Models\Client' => 'Clients',
                                    'App\Models\Staff' => 'Staff',
                                    'App\Models\User' => 'Users'
                                ];
                            @endphp
                            @foreach($entityLabels as $entityType => $label)
                                @if(isset($entityTypeCounts[$entityType]) && $entityTypeCounts[$entityType] > 0)
                                    <a href="{{ request()->fullUrlWithQuery(['entity_type' => $entityType, 'page' => 1]) }}"
                                       class="list-group-item list-group-item-action {{ request('entity_type') == $entityType ? 'active' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ $label }}</span>
                                            <span class="badge bg-info">{{ $entityTypeCounts[$entityType] }}</span>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    @if(request('type') || request('entity_type') || request('search'))
                        <div class="mt-4">
                            <a href="{{ \App\Helpers\RouteHelper::url('documents.gallery') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle me-2"></i>Clear All Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Gallery -->
        <div class="col-lg-9 col-md-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 document-gallery-header">
                <div>
                    <p class="text-muted mb-0">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        Showing <strong>{{ $documents->firstItem() ?? 0 }}-{{ $documents->lastItem() ?? 0 }}</strong> of <strong>{{ $documents->total() }}</strong> documents
                    </p>
                </div>
            </div>

            <!-- Gallery Grid -->
            @if($documents->count() > 0)
                <div class="row g-4" id="documentGrid">
                    @foreach($documents as $document)
                        <div class="col-xl-3 col-lg-4 col-md-6 document-item d-flex"
                             data-type="{{ $document->document_type }}"
                             data-entity="{{ $document->documentable_type }}"
                             data-name="{{ strtolower($document->name) }}">
                            <div class="card document-card w-100 shadow-sm">
                                <!-- Document Preview -->
                                <div class="document-preview">
                                    @if(str_starts_with($document->file_type, 'image/'))
                                        <img src="{{ asset('storage/' . $document->file_path) }}"
                                             alt="{{ $document->name }}"
                                             class="document-image"
                                             onerror="this.src='/Image-not-found.png'; this.onerror=null;">
                                    @else
                                        <div class="document-icon">
                                            @if($document->file_type == 'application/pdf')
                                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                                            @elseif(str_contains($document->file_type, 'word') || str_contains($document->file_type, 'document'))
                                                <i class="bi bi-file-earmark-word-fill text-primary"></i>
                                            @else
                                                <i class="bi bi-file-earmark-fill text-secondary"></i>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="document-overlay">
                                        <div class="document-actions">
                                            <a href="{{ \App\Helpers\RouteHelper::url('documents.download', $document) }}"
                                               class="btn btn-sm btn-light"
                                               target="_blank"
                                               title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @if(auth()->user()->can('delete documents'))
                                                <button type="button"
                                                        class="btn btn-sm btn-light btn-delete-document"
                                                        data-document-id="{{ $document->id }}"
                                                        data-document-name="{{ $document->name }}"
                                                        title="Delete">
                                                    <i class="bi bi-trash text-danger"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Info -->
                                <div class="card-body">
                                    <h6 class="card-title" title="{{ $document->name }}">
                                        {{ Str::limit($document->name, 40) }}
                                    </h6>
                                    <div class="document-meta">
                                        <span class="badge d-inline-block">
                                            <i class="bi bi-tag-fill me-1"></i>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}
                                        </span>
                                        <div class="small mt-2">
                                            <div>
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <span>{{ $document->created_at->format('M d, Y') }}</span>
                                            </div>
                                            @if($document->documentable)
                                                <div>
                                                    <i class="bi bi-tag me-1"></i>
                                                    <span>{{ class_basename($document->documentable_type) }}</span>
                                                </div>
                                            @endif
                                            @if($document->uploadedBy)
                                                <div>
                                                    <i class="bi bi-person me-1"></i>
                                                    <span>{{ Str::limit($document->uploadedBy->name, 15) }}</span>
                                                </div>
                                            @endif
                                            @if($document->file_size)
                                                <div>
                                                    <i class="bi bi-file-earmark me-1"></i>
                                                    <span>{{ number_format($document->file_size / 1024, 1) }} KB</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($document->description)
                                        <p class="card-text mt-2 mb-0">
                                            {{ Str::limit($document->description, 50) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $documents->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-x icon-48px empty-state-icon-medium text-muted"></i>
                    <h4 class="mt-3 text-muted">No Documents Found</h4>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Search functionality
        let searchTimeout;
        document.getElementById('searchInput')?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchValue = this.value;

            searchTimeout = setTimeout(function() {
                if (searchValue.length >= 2 || searchValue.length === 0) {
                    const url = new URL(window.location.href);
                    if (searchValue) {
                        url.searchParams.set('search', searchValue);
                    } else {
                        url.searchParams.delete('search');
                    }
                    url.searchParams.set('page', '1');
                    window.location.href = url.toString();
                }
            }, 500);
        });

        // Delete document functionality
        document.querySelectorAll('.btn-delete-document').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const documentId = this.getAttribute('data-document-id');
                const documentName = this.getAttribute('data-document-name');

                if (confirm('Are you sure you want to delete "' + documentName + '"? This action cannot be undone.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ \App\Helpers\RouteHelper::url("documents.destroy", ":id") }}'.replace(':id', documentId);

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
    @vite(['resources/js/document-gallery.js'])
@endpush
