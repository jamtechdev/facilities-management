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
                {{-- <div class="profile-avatar">
                {{ strtoupper(substr($staff->name, 0, 1)) }}
            </div> --}}
                <div class="profile-avatar position-relative">
                    <div id="avatar-container" class="w-100 h-100">
                        @if ($user->avatar)
                            <img id="profile-preview" src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle">
                        @else
                            <span id="avatar-initial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>

                    <label for="profile_image_input" class="profile-image-edit-btn">
                        <i class="bi bi-camera-fill"></i>
                    </label>
                    <input type="file" id="profile_image_input" class="d-none" accept="image/*">
                </div>
                <div class="profile-info">
                    <h1>{{ $staff->name }}</h1>
                    <p>Update your personal information and upload compliance documents</p>
                </div>
            </div>
        </div>

        <div id="alert-container"></div>

        <ul class="nav nav-tabs profile-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-info-tab" data-bs-toggle="tab" data-bs-target="#profileInfoTab"
                    type="button" role="tab">
                    Personal Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-docs-tab" data-bs-toggle="tab" data-bs-target="#profileDocsTab"
                    type="button" role="tab">
                    Documents
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="profileInfoTab" role="tabpanel" aria-labelledby="profile-info-tab">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="profile-card">
                            <div class="profile-card-header">
                                <h5><i class="bi bi-person-circle"></i> Profile Information</h5>
                            </div>
                            <div class="profile-card-body">
                                <form id="profileForm" class="profile-form">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $staff->name }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="mobile" class="form-label">Mobile</label>
                                            <input type="text" class="form-control" id="mobile" name="mobile"
                                                value="{{ $staff->mobile }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ $staff->email }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="3">{{ $staff->address }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                            <h6 class="mb-3">Change Password (Optional)</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="password" class="form-label">New Password</label>
                                                    <input type="password" class="form-control" id="password"
                                                        name="password">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="password_confirmation" class="form-label">Confirm
                                                        Password</label>
                                                    <input type="password" class="form-control" id="password_confirmation"
                                                        name="password_confirmation">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save me-2"></i>Update Profile
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
                                <h5><i class="bi bi-info-circle"></i> Account Details</h5>
                            </div>
                            <div class="profile-card-body">
                                <div class="account-details-item">
                                    <p class="account-details-label">Hourly Rate</p>
                                    <p class="account-details-value highlight">
                                        ${{ number_format($staff->hourly_rate ?? 0, 2) }}</p>
                                </div>
                                <div class="account-details-item">
                                    <p class="account-details-label">Assigned Weekly Hours</p>
                                    <p class="account-details-value">
                                        {{ number_format($staff->assigned_weekly_hours ?? 0, 2) }} hours</p>
                                </div>
                                <div class="account-details-item">
                                    <p class="account-details-label">Assigned Monthly Hours</p>
                                    <p class="account-details-value">
                                        {{ number_format($staff->assigned_monthly_hours ?? 0, 2) }} hours</p>
                                </div>
                                <div class="account-details-item">
                                    <p class="account-details-label">Status</p>
                                    <p class="mb-0">
                                        @if ($staff->is_active)
                                            <span class="status-badge active">Active</span>
                                        @else
                                            <span class="status-badge inactive">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="profileDocsTab" role="tabpanel" aria-labelledby="profile-docs-tab">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="profile-card mb-4">
                            <div class="profile-card-header">
                                <h5><i class="bi bi-cloud-upload"></i> Upload Document</h5>
                            </div>
                            <div class="profile-card-body">
                                <form id="documentUploadForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small">Document Name</label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="e.g., Photo ID" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Document Type</label>
                                        <select class="form-select" name="document_type">
                                            <option value="">Select type...</option>
                                            <option value="id">ID Proof</option>
                                            <option value="certificate">Certificate</option>
                                            <option value="agreement">Agreement</option>
                                            <option value="signed_form">Signed Form</option>
                                            <option value="image">Image</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Description (optional)</label>
                                        <textarea class="form-control" name="description" rows="2" placeholder="Add a short note..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">Upload file</label>
                                        <input type="file" class="form-control" name="document"
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100" id="documentUploadBtn">
                                        <i class="bi bi-cloud-upload me-2"></i>Upload Document
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="profile-card">
                            <div class="profile-card-header">
                                <h5><i class="bi bi-folder"></i> Your Files</h5>
                                <span class="badge badge-gradient-green">{{ $documents->count() }} files</span>
                            </div>
                            <div class="profile-card-body">
                                @forelse($documents as $document)
                                    <div class="document-item">
                                        <div class="document-item-info">
                                            <h6>{{ $document->name }}</h6>
                                            <small>
                                                {{ strtoupper($document->document_type ?? 'other') }} •
                                                {{ $document->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="document-item-actions">
                                            <a href="{{ \App\Helpers\RouteHelper::url('profile.documents.download', $document) }}"
                                                class="btn btn-light" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <button class="btn btn-light text-danger delete-document-btn"
                                                data-delete-url="{{ \App\Helpers\RouteHelper::url('profile.documents.destroy', $document) }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="bi bi-inbox empty-state-icon-large"></i>
                                        <p class="text-muted mt-3 mb-0">No documents uploaded yet.</p>
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
            (function() {
                // Prevent duplicate event listeners
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
                            method: 'PUT',
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
                            showAlert('success', data.message);
                        } else {
                            showAlert('danger', data.message || 'Failed to update profile');
                        }
                    } catch (error) {
                        showAlert('danger', 'Failed to update profile: ' + (error.message || 'Unknown error'));
                    } finally {
                        isSubmitting = false;
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
            })();

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
                        const response = await fetch(
                            '{{ \App\Helpers\RouteHelper::url('profile.documents.store') }}', {
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
                            showAlert('success', data.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', data.message || 'Failed to upload document');
                        }
                    } catch (error) {
                        showAlert('danger', 'Failed to upload document: ' + (error.message || 'Unknown error'));
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
            }

            document.querySelectorAll('.delete-document-btn').forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();
                    if (!confirm('Delete this document?')) {
                        return;
                    }

                    const deleteUrl = this.dataset.deleteUrl;
                    try {
                        const response = await fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            showAlert('success', data.message);
                            setTimeout(() => location.reload(), 500);
                        } else {
                            showAlert('danger', data.message);
                        }
                    } catch (error) {
                        showAlert('danger', 'Failed to delete document: ' + error.message);
                    }
                });
            });

            const imageInput = document.getElementById('profile_image_input');
            const avatarContainer = document.getElementById('avatar-container');

            if (imageInput) {
                imageInput.addEventListener('change', async function() {
                    if (!this.files || !this.files[0]) return;

                    const formData = new FormData();
                    formData.append('avatar', this.files[0]);

                    try {

                        avatarContainer.style.opacity = '0.5';

                        const response = await fetch('{{ \App\Helpers\RouteHelper::url('profile.image.update') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {

                            avatarContainer.innerHTML =
                                `<img id="profile-preview" src="${data.url}" class="rounded-circle">`;
                            showAlert('success', 'Profile picture updated!');
                        } else {
                            showAlert('danger', data.message || 'Upload failed');
                        }
                    } catch (error) {
                        showAlert('danger', 'Error uploading image');
                    } finally {
                        avatarContainer.style.opacity = '1';
                    }
                });
            }
        </script>
    @endpush
@endsection
