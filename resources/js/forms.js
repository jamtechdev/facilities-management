/**
 * Forms JavaScript
 * Handles form submissions, validation, and AJAX requests
 */

// Initialize all forms on page load
document.addEventListener('DOMContentLoaded', function() {
    initFormSubmissions();
});

/**
 * Initialize form submissions with AJAX
 */
function initFormSubmissions() {
    // Handle create forms
    const createForms = document.querySelectorAll('form[id$="Form"]');
    
    createForms.forEach(form => {
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn && !form.dataset.initialized) {
            form.dataset.initialized = 'true';
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(form, submitBtn);
            });
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(form, submitBtn) {
    const formData = new FormData(form);
    const submitBtnText = submitBtn.innerHTML;
    const method = form.method.toUpperCase();
    const action = form.action;
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    // Clear previous validation errors
    clearValidationErrors(form);
    
    // Determine HTTP method override
    const headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    };
    
    if (method === 'POST' && form.querySelector('input[name="_method"]')) {
        headers['X-HTTP-Method-Override'] = form.querySelector('input[name="_method"]').value;
        headers['Content-Type'] = 'multipart/form-data';
    } else if (method === 'PUT' || method === 'PATCH' || method === 'DELETE') {
        headers['X-HTTP-Method-Override'] = method;
        headers['Content-Type'] = 'multipart/form-data';
    }
    
    // Make AJAX request
    axios.post(action, formData, { headers })
        .then(function(response) {
            if (response.data.success) {
                if (response.data.redirect) {
                    window.location.href = response.data.redirect;
                } else {
                    // Reload page if no redirect
                    window.location.reload();
                }
            }
        })
        .catch(function(error) {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = submitBtnText;
            
            // Handle validation errors
            if (error.response?.status === 422) {
                displayValidationErrors(error.response.data.errors, form);
            } else {
                alert(error.response?.data?.message || 'An error occurred. Please try again.');
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
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }
                })
                .catch(function(error) {
                    alert(error.response?.data?.message || 'Failed to delete item');
                });
            }
        });
    });
}

// Initialize delete confirmations on page load
document.addEventListener('DOMContentLoaded', initDeleteConfirmations);
