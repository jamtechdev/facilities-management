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
            <form id="uploadDocumentForm" enctype="multipart/form-data">
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
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
            
            axios.post('{{ route("admin.documents.store") }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(function(response) {
                if (response.data.success) {
                    location.reload();
                }
            })
            .catch(function(error) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert(error.response?.data?.message || 'Failed to upload document');
            });
        });
    }
});
</script>

