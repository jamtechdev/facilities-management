<!-- Add Communication Modal -->
<div class="modal fade" id="addCommunicationModal" tabindex="-1" aria-labelledby="addCommunicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCommunicationModalLabel">
                    <i class="bi bi-chat-dots me-2"></i>Add Communication
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCommunicationForm">
                @csrf
                <input type="hidden" name="communicable_type" value="{{ get_class($communicable) }}">
                <input type="hidden" name="communicable_id" value="{{ $communicable->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="communication_type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="communication_type" name="type" required>
                            <option value="call">Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="note">Note</option>
                        </select>
                    </div>
                    <div class="mb-3" id="email_to_field" style="display: none;">
                        <label for="email_to" class="form-label">Email To <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email_to" name="email_to" value="{{ $communicable->email ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="communication_subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="communication_subject" name="subject" placeholder="Enter subject">
                    </div>
                    <div class="mb-3">
                        <label for="communication_message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="communication_message" name="message" rows="5" required placeholder="Enter message or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save Communication
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addCommunicationForm');
    const typeSelect = document.getElementById('communication_type');
    const emailToField = document.getElementById('email_to_field');

    // Show/hide email field based on type
    typeSelect.addEventListener('change', function() {
        if (this.value === 'email') {
            emailToField.style.display = 'block';
            document.getElementById('email_to').required = true;
        } else {
            emailToField.style.display = 'none';
            document.getElementById('email_to').required = false;
        }
    });

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            axios.post('{{ route("admin.communications.store") }}', formData, {
                headers: {
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
                alert(error.response?.data?.message || 'Failed to save communication');
            });
        });
    }
});
</script>

