/**
 * Communications Modal JavaScript
 * Handles communication form submission and interactions
 */

(function() {
    'use strict';

    // Global flag to prevent multiple initializations across different includes
    if (window.communicationsModalInitialized) {
        return; // Already initialized
    }
    
    let isInitialized = false;

    /**
     * Initialize communications modal
     */
    function initCommunicationsModal() {
        if (isInitialized) return;

        const form = document.getElementById('addCommunicationForm');
        if (!form || form.dataset.handlerAttached === 'true') return;

        form.dataset.handlerAttached = 'true';
        isInitialized = true;
        window.communicationsModalInitialized = true;

        const typeSelect = document.getElementById('communication_type');
        const emailToField = document.getElementById('email_to_field');

        // Show/hide email field based on type
        if (typeSelect && emailToField) {
            typeSelect.addEventListener('change', function() {
                if (this.value === 'email') {
                    emailToField.style.display = 'block';
                    const emailInput = document.getElementById('email_to');
                    if (emailInput) emailInput.required = true;
                } else {
                    emailToField.style.display = 'none';
                    const emailInput = document.getElementById('email_to');
                    if (emailInput) emailInput.required = false;
                }
            });
        }

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Prevent duplicate submissions
            if (form.dataset.submitting === 'true') {
                return false;
            }
            form.dataset.submitting = 'true';

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            // Get route from form action, data attribute, or window object
            const actionUrl = form.action || form.dataset.actionUrl || window.communicationRoute;
            
            if (!actionUrl) {
                console.error('Communication route not found');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                return;
            }

            // Use fetch or axios
            if (typeof axios !== 'undefined') {
                axios.post(actionUrl, formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                })
                .then(function(response) {
                    form.dataset.submitting = 'false';
                    if (response.data.success) {
                        location.reload();
                    }
                })
                .catch(function(error) {
                    form.dataset.submitting = 'false';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    const message = error.response?.data?.message || 'Failed to save communication';
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', message);
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                });
            } else {
                fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    form.dataset.submitting = 'false';
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    form.dataset.submitting = 'false';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    const message = 'Failed to save communication';
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', message);
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                });
            }
        });
    }

    // Initialize when DOM is ready - but only once globally
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCommunicationsModal);
    } else {
        initCommunicationsModal();
    }
})();

