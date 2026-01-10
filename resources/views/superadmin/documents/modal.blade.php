<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">
                    <i class="bi bi-upload me-2"></i>Upload Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadDocumentForm" method="POST" action="#" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="documentable_type" value="{{ get_class($documentable) }}">
                <input type="hidden" name="documentable_id" value="{{ $documentable->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_name" class="form-label">Document Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_name" name="name" required placeholder="Enter document name">
                    </div>
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type</label>
                        <select class="form-select" id="document_type" name="document_type">
                            <option value="agreement">Agreement</option>
                            <option value="proposal">Proposal</option>
                            <option value="signed_form">Signed Form</option>
                            <option value="image">Image</option>
                            <option value="id">ID</option>
                            <option value="certificate">Certificate</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="document_file" class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="document_file" name="document" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                        <small class="form-text text-muted">Max file size: 10MB. Allowed: PDF, DOC, DOCX, JPG, PNG, GIF</small>
                    </div>
                    <div class="mb-3">
                        <label for="document_description" class="form-label">Description</label>
                        <textarea class="form-control" id="document_description" name="description" rows="3" placeholder="Optional description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadDocumentForm');

    if (form && !form.dataset.initialized) {
        form.dataset.initialized = 'true';
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Prevent double submission
            if (isSubmitting) {
                return false;
            }
            isSubmitting = true;

            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

            axios.post('{{ \App\Helpers\RouteHelper::url('documents.store') }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                if (response.data.success) {
                    // Show success message
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('success', response.data.message || 'Document uploaded successfully');
                    } else if (typeof window.toastr !== 'undefined') {
                        window.toastr.success(response.data.message || 'Document uploaded successfully');
                    } else {
                        alert(response.data.message || 'Document uploaded successfully');
                    }

                    // Close modal and reload after short delay
                    setTimeout(function() {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal'));
                        if (modal) {
                            modal.hide();
                        }
                        location.reload();
                    }, 500);
                } else {
                    // Handle case where success is false
                    const errorMessage = response.data.message || 'Failed to upload document';
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', errorMessage);
                    } else if (typeof window.toastr !== 'undefined') {
                        window.toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(function(error) {
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                // Handle validation errors
                if (error.response?.status === 422 && error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    let firstErrorMessage = null;

                    // Display field errors and collect first error message
                    Object.keys(errors).forEach(field => {
                        const fieldElement = form.querySelector(`[name="${field}"]`);
                        if (fieldElement) {
                            fieldElement.classList.add('is-invalid');
                            const errorMessagesArray = Array.isArray(errors[field]) ? errors[field] : [errors[field]];

                            // Get first error message for toast
                            if (!firstErrorMessage && errorMessagesArray.length > 0) {
                                firstErrorMessage = errorMessagesArray[0];
                            }

                            // Add invalid feedback (only first error per field)
                            if (errorMessagesArray.length > 0) {
                                // Remove existing feedback if any
                                const existingFeedback = fieldElement.parentElement.querySelector('.invalid-feedback');
                                if (existingFeedback) {
                                    existingFeedback.remove();
                                }

                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = errorMessagesArray[0];
                                fieldElement.parentElement.appendChild(feedback);
                            }
                        } else {
                            // If field not found, use first error message
                            const errorMessagesArray = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
                            if (!firstErrorMessage && errorMessagesArray.length > 0) {
                                firstErrorMessage = errorMessagesArray[0];
                            }
                        }
                    });

                    // Show only first error message in toast (to avoid duplicates)
                    const messageToShow = error.response?.data?.message || firstErrorMessage || 'Validation failed. Please check the form.';
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', messageToShow);
                    } else if (typeof window.toastr !== 'undefined') {
                        window.toastr.error(messageToShow);
                    } else {
                        alert(messageToShow);
                    }
                } else {
                    // Handle other errors
                    const errorMessage = error.response?.data?.message || error.message || 'Failed to upload document';
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', errorMessage);
                    } else if (typeof window.toastr !== 'undefined') {
                        window.toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                }
            });
        });
    }
});
</script>
