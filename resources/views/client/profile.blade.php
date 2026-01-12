@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
    @vite(['resources/css/utilities.css', 'resources/css/profile.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    {{ strtoupper(substr($client->contact_person ?? ($client->company_name ?? 'C'), 0, 1)) }}
                </div>
                <div class="profile-info">
                    <h1>{{ $client->company_name }}</h1>
                    <p>Contact: {{ $client->contact_person }}</p>
                </div>
            </div>
        </div>

        <div id="alert-container"></div>

        <ul class="nav nav-tabs profile-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-info-tab" data-bs-toggle="tab" data-bs-target="#profileInfoTab"
                    type="button" role="tab">
                    Company & Billing Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-docs-tab" data-bs-toggle="tab" data-bs-target="#profileDocsTab"
                    type="button" role="tab">
                    Documents
                </button>
            </li>
        </ul>

        <div class="tab-content mt-4">
            <!-- Profile Info Tab -->
            <div class="tab-pane fade show active" id="profileInfoTab">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="profile-card">
                            <div class="profile-card-header">
                                <h5><i class="bi bi-building"></i> Company Information</h5>
                            </div>
                            <div class="profile-card-body">
                                <form id="profileForm">
                                    @csrf
                                    @method('PUT')

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Company Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="company_name"
                                                value="{{ old('company_name', $client->company_name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Contact Person <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="contact_person"
                                                value="{{ old('contact_person', $client->contact_person) }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ old('email', $client->email) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone"
                                                value="{{ old('phone', $client->phone) }}">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address" rows="3">{{ old('address', $client->address) }}</textarea>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save me-2"></i> Update Profile
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="profile-card">
                            <div class="profile-card-header">
                                <h5><i class="bi bi-info-circle"></i> Account Summary</h5>
                            </div>
                            <div class="profile-card-body">
                                <div class="account-details-item">
                                    <p class="account-details-label">Status</p>
                                    <p class="mb-0">
                                        <span class="status-badge {{ $client->is_active ? 'active' : 'inactive' }}">
                                            {{ $client->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="account-details-item">
                                    <p class="account-details-label">Member Since</p>
                                    <p class="account-details-value">{{ $client->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="account-details-item">
                                    <p class="account-details-label">Total Documents</p>
                                    <p class="account-details-value highlight">{{ $documents->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="profileDocsTab" role="tabpanel">
                <div class="row">
                    <!-- Upload Section -->
                    <div class="col-lg-6">
                        <div class="profile-card mb-4">
                            <div class="profile-card-header">
                                <h5><i class="bi bi-cloud-upload"></i> Upload New Document</h5>
                            </div>
                            <div class="profile-card-body">
                                <form id="documentUploadForm" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label">Document Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="e.g., GST Certificate, Agreement" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Document Type</label>
                                        <select class="form-select" name="document_type">
                                            <option value="">Select type (optional)</option>
                                            <option value="id">ID Proof</option>
                                            <option value="certificate">Certificate</option>
                                            <option value="agreement">Agreement</option>
                                            <option value="proposal">Proposal</option>
                                            <option value="signed_form">Signed Form</option>
                                            <option value="image">Company Logo / Image</option>
                                            <option value="other" selected>Other</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description (optional)</label>
                                        <textarea class="form-control" name="description" rows="2" placeholder="Brief note about this document..."></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">File <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="document"
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                        <div class="form-text">Max 10MB • PDF, DOC, DOCX, JPG, PNG allowed</div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100" id="documentUploadBtn">
                                        <i class="bi bi-cloud-upload me-2"></i> Upload Document
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Documents List -->
                    <div class="col-lg-6">
                        <div class="profile-card">
                            <div class="profile-card-header d-flex justify-content-between align-items-center">
                                <h5><i class="bi bi-folder"></i> Uploaded Documents</h5>
                                <span class="badge badge-gradient-green">
                                    {{ $documents->count() }} file{{ $documents->count() != 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div class="profile-card-body profile-card-body-scrollable">
                                @forelse($documents as $document)
                                    <div
                                        class="document-item d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="document-item-info">
                                            <h6 class="mb-1">{{ $document->name }}</h6>
                                            <small class="text-muted">
                                                {{ ucfirst($document->document_type ?? 'other') }} •
                                                {{ $document->created_at->format('M d, Y') }} •
                                                {{ number_format($document->file_size / 1024, 1) }} KB
                                            </small>
                                        </div>
                                        <div class="document-item-actions">
                                            <a href="{{ \App\Helpers\RouteHelper::url('profile.documents.download', $document->id) }}"
                                                class="btn btn-sm btn-outline-primary me-1" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger delete-document-btn"
                                                data-delete-url="{{ \App\Helpers\RouteHelper::url('profile.documents.destroy', $document->id) }}"
                                                title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="bi bi-inbox empty-state-icon-xl"></i>
                                        <p class="text-muted mt-3">No documents uploaded yet.</p>
                                        <p class="text-muted small">Upload your company documents like GST, agreements,
                                            etc.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Profile Update - Prevent duplicate listeners
            (function() {
                const profileForm = document.getElementById('profileForm');
                if (!profileForm || profileForm.dataset.listenerAttached) {
                    return;
                }
                profileForm.dataset.listenerAttached = 'true';

                let isSubmitting = false;

                profileForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    // Prevent duplicate submissions
                    if (isSubmitting) {
                        return;
                    }
                    isSubmitting = true;

                    const formData = new FormData(this);
                    const btn = this.querySelector('button[type="submit"]');
                    const originalText = btn.innerHTML;

                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                    try {
                        const response = await fetch('{{ \App\Helpers\RouteHelper::url('profile.update') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.success) {
                            showAlert('success', data.message || 'Profile updated successfully!');
                        } else {
                            let errorMsg = data.message || 'Update failed';
                            if (data.errors) {
                                errorMsg += '<br><small>' + Object.values(data.errors).flat().join('<br>') + '</small>';
                            }
                            showAlert('danger', errorMsg);
                        }
                    } catch (error) {
                        showAlert('danger', 'Network error. Please try again.');
                    } finally {
                        isSubmitting = false;
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
            })();

            // Document Upload
            const documentForm = document.getElementById('documentUploadForm');
            if (documentForm) {
                documentForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const btn = document.getElementById('documentUploadBtn');
                    const originalText = btn.innerHTML;

                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

                    try {
                        const response = await fetch('{{ \App\Helpers\RouteHelper::url('profile.documents.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.success) {
                            showAlert('success', data.message || 'Document uploaded successfully!');
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            let errorMsg = data.message || 'Upload failed';
                            if (data.errors) {
                                errorMsg += '<br><small>' + Object.values(data.errors).flat().join('<br>') +
                                    '</small>';
                            }
                            showAlert('danger', errorMsg);
                        }
                    } catch (error) {
                        showAlert('danger', 'Upload failed. Please try again.');
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
            }

            // Document Delete
            document.querySelectorAll('.delete-document-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    if (!confirm(
                            'Are you sure you want to delete this document? This action cannot be undone.'
                            )) {
                        return;
                    }

                    const url = this.dataset.deleteUrl;

                    try {
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            showAlert('success', data.message || 'Document deleted successfully.');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            showAlert('danger', data.message || 'Failed to delete document.');
                        }
                    } catch (error) {
                        showAlert('danger', 'Network error. Please try again.');
                    }
                });
            });

            // Alert Function - using toastr
            function showAlert(type, message) {
                if (typeof showToast !== 'undefined') {
                    showToast(type, message);
                } else if (typeof toastr !== 'undefined') {
                    const toastType = type === 'danger' ? 'error' : type;
                    toastr[toastType](message);
                } else {
                    alert(message);
                }
            }

            // Legacy code removed - keeping for reference
            if (false) {
                    setTimeout(() => {
                        const alert = container.querySelector('.alert');
                        if (alert) alert.remove();
                    }, 5000);
                }
            }
        </script>
    @endpush
@endsection
