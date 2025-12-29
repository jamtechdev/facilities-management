/**
 * Inventory Form Handler
 * Handles inventory create and edit form submissions
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('inventoryForm');
    if (!form) return;

    const submitBtn = form.querySelector('button[type="submit"]');
    const formErrors = document.getElementById('formErrors');
    let isSubmitting = false;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (isSubmitting) return;
        isSubmitting = true;

        // Clear previous errors
        if (formErrors) {
            formErrors.classList.add('d-none');
            formErrors.innerHTML = '';
        }
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        // Disable submit button and show loading state
        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        const isEdit = form.action.includes('/edit') || form.method === 'PUT' || form.querySelector('input[name="_method"]');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + (isEdit ? 'Updating...' : 'Saving...');

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                if (typeof showToast !== 'undefined') {
                    showToast('success', data.message);
                } else if (typeof toastr !== 'undefined') {
                    toastr.success(data.message);
                } else {
                    alert(data.message);
                }

                // Redirect after short delay
                setTimeout(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Get index route from form data attribute or default
                        const indexRoute = form.dataset.indexRoute || '/admin/inventory';
                        window.location.href = indexRoute;
                    }
                }, 500);
            } else {
                // Handle validation errors
                if (data.errors) {
                    let errorHtml = '<ul class="mb-0">';
                    Object.keys(data.errors).forEach(field => {
                        const errorMessages = Array.isArray(data.errors[field]) ? data.errors[field] : [data.errors[field]];
                        errorMessages.forEach(error => {
                            errorHtml += `<li>${error}</li>`;
                        });

                        const fieldElement = form.querySelector(`[name="${field}"]`);
                        if (fieldElement) {
                            fieldElement.classList.add('is-invalid');
                            const feedback = fieldElement.parentElement.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.textContent = errorMessages[0];
                            }
                        }
                    });
                    errorHtml += '</ul>';
                    if (formErrors) {
                        formErrors.innerHTML = errorHtml;
                        formErrors.classList.remove('d-none');
                    }
                }

                // Show error message
                const errorMessage = data.message || 'Please fix the errors and try again';
                showToast?.('error', errorMessage) ||
                toastr?.error(errorMessage) ||
                alert(errorMessage);
            }
        } catch (error) {
            const errorMessage = isEdit
                ? 'Failed to update inventory item: ' + error.message
                : 'Failed to save inventory item: ' + error.message;
            showToast?.('error', errorMessage) ||
            toastr?.error(errorMessage) ||
            alert(errorMessage);
        } finally {
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
