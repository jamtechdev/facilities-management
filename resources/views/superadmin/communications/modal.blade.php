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
            <form id="addCommunicationForm" action="{{ \App\Helpers\RouteHelper::url('communications.store') }}">
                @csrf
                <input type="hidden" name="communicable_type" value="{{ get_class($communicable) }}">
                <input type="hidden" name="communicable_id" value="{{ $communicable->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="communication_type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="communication_type" name="type" required>
                            <option value="email">Email</option>
                            <option value="call">Call</option>
                            <option value="meeting">Meeting</option>
                            <option value="note">Note</option>
                        </select>
                    </div>
                    <div class="mb-3 field-hidden" id="email_to_field">
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

@once
@push('scripts')
    <script>
        // Pass route to JS (only once per page, even if modal is included multiple times)
        if (typeof window.communicationRoute === 'undefined') {
            window.communicationRoute = '{{ \App\Helpers\RouteHelper::url("communications.store") }}';
        }
    </script>
    @vite(['resources/js/pages/communications.js'])
@endpush
@endonce

