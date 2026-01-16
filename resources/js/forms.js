/**
 * Forms JavaScript
 * Handles form submissions, validation, and AJAX requests
 */

// Initialize all forms on page load
document.addEventListener('DOMContentLoaded', function() {
    initFormSubmissions();
});

// Prevent duplicate initialization
let formsInitialized = false;

/**
 * Initialize form submissions with AJAX
 */
function initFormSubmissions() {
    if (formsInitialized) return;

    // Exclude forms that have their own handlers
    const forms = document.querySelectorAll('form[id$="Form"]:not(#addCommunicationForm):not(#inventoryForm):not(#profileForm):not(#clockInForm)');

    forms.forEach(form => {
        const submitBtn = form.querySelector('button[type="submit"]');

        if (submitBtn && !form.dataset.initialized) {
            form.dataset.initialized = 'true';

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(form, submitBtn);
            });
        }
    });

    formsInitialized = true;
}

/**
 * Handle form submission
 */
function handleFormSubmit(form, submitBtn) {
    // Prevent duplicate submissions
    if (form.dataset.submitting === 'true') {
        return;
    }
    form.dataset.submitting = 'true';

    const formData = new FormData(form);
    const submitBtnText = submitBtn.innerHTML;
    const method = form.method.toUpperCase();
    let action = form.action;

    // Disable submit button
    submitBtn.disabled = true;
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

    // Show global loader (only if preloader is not visible)
    if (typeof window.showGlobalLoader === 'function') {
        const preloader = document.getElementById('preloader');
        const isPreloaderVisible = preloader &&
            !preloader.classList.contains('hide') &&
            preloader.style.display !== 'none' &&
            window.getComputedStyle(preloader).display !== 'none';

        if (!isPreloaderVisible) {
            window.showGlobalLoader('Processing...');
        }
    }

    // Clear previous validation errors
    clearValidationErrors(form);

    // Get the actual HTTP method (check for _method input for Laravel method spoofing)
    let httpMethod = method;
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) {
        httpMethod = methodInput.value.toUpperCase();
    }

    const headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
    };

    // Make AJAX request with proper method
    // For PUT/PATCH/DELETE, Laravel uses method spoofing via _method field in formData
    // So we always POST, but the _method field in FormData tells Laravel the actual method
    // The formData already contains _method from the hidden input, so just POST it
    axios.post(action, formData, { headers })
        .then(function(response) {
            if (response.data.success) {
                // Show success message
                const successMessage = response.data.message || 'Operation completed successfully.';
                if (typeof window.showToast !== 'undefined') {
                    window.showToast('success', successMessage);
                } else if (typeof window.toastr !== 'undefined') {
                    window.toastr.success(successMessage);
                }

                // Hide global loader before redirect
                if (typeof window.hideGlobalLoader === 'function') {
                    window.hideGlobalLoader();
                }
                // Redirect or reload after a short delay to show toast
                setTimeout(function () {
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        // Reload page if no redirect
                        window.location.reload();
                    }
                }, 500); // Small delay to show toast
            } else {
                // Handle case where success is false
                const errorMessage = response.data.message || 'Operation failed. Please try again.';
                if (typeof window.showToast !== 'undefined') {
                    window.showToast('error', errorMessage);
                } else if (typeof window.toastr !== 'undefined') {
                    window.toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                // Hide global loader
                if (typeof window.hideGlobalLoader === 'function') {
                    window.hideGlobalLoader();
                }
                // Re-enable submit button
                form.dataset.submitting = 'false';
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtnText;
            }
        })
        .catch(function(error) {
            // Hide global loader
            if (typeof window.hideGlobalLoader === 'function') {
                window.hideGlobalLoader();
            }
            // Re-enable submit button
            form.dataset.submitting = 'false';
            submitBtn.disabled = false;
            submitBtn.innerHTML = submitBtnText;

            // Handle validation errors
            if (error.response?.status === 422) {
                displayValidationErrors(error.response.data.errors, form);
            } else {
                const errorMessage = error.response?.data?.message || error.message || 'An error occurred. Please try again.';
                if (typeof window.showToast !== 'undefined') {
                    window.showToast('error', errorMessage);
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
}

/**
 * Clear validation errors
 */
function clearValidationErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    form.querySelectorAll('.invalid-feedback').forEach(el => {
        el.remove();
    });
}

/**
 * Display validation errors
 */
function displayValidationErrors(errors, form) {
    Object.keys(errors).forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[field][0];
            input.parentNode.appendChild(errorDiv);
        }
    });
}

/**
 * Initialize delete confirmations
 */
function initDeleteConfirmations() {
    document.querySelectorAll('[data-delete-url]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = btn.getAttribute('data-delete-url');
            const message = btn.getAttribute('data-delete-message') || 'Are you sure you want to delete this item?';

            if (confirm(message)) {
                axios.delete(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(function(response) {
                    if (response.data.success) {
                        // Show success message
                        const successMessage = response.data.message || 'Item deleted successfully.';
                        if (typeof window.showToast !== 'undefined') {
                            window.showToast('success', successMessage);
                        } else if (typeof window.toastr !== 'undefined') {
                            window.toastr.success(successMessage);
                        }

                        // Redirect or reload after a short delay
                        setTimeout(function () {
                            if (response.data.redirect) {
                                window.location.href = response.data.redirect;
                            } else {
                                window.location.reload();
                            }
                        }, 500);
                    }
                })
                .catch(function(error) {
                    if (typeof window.showToast !== 'undefined') {
                        window.showToast('error', error.response?.data?.message || 'Failed to delete item');
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(error.response?.data?.message || 'Failed to delete item');
                    } else {
                        alert(error.response?.data?.message || 'Failed to delete item');
                    }
                });
            }
        });
    });
}

// Initialize delete confirmations on page load
document.addEventListener('DOMContentLoaded', initDeleteConfirmations);
