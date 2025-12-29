/**
 * Flash Messages Handler
 * Handles display of flash messages from server
 * Reads messages from data attributes on body element
 */

(function() {
    'use strict';

    /**
     * Initialize flash messages on page load
     */
    function initFlashMessages() {
        // Wait for showToast function to be available (check window object)
        if (typeof window.showToast === 'undefined' && typeof showToast === 'undefined') {
            setTimeout(initFlashMessages, 100);
            return;
        }
        
        // Use window.showToast if available, otherwise fallback to global showToast
        const showToastFn = window.showToast || (typeof showToast !== 'undefined' ? showToast : null);
        
        if (!showToastFn) {
            setTimeout(initFlashMessages, 100);
            return;
        }

        const body = document.body;
        
        // Get flash messages from data attributes
        const successMessage = body.dataset.flashSuccess;
        const errorMessage = body.dataset.flashError;
        const errorsJson = body.dataset.flashErrors;

        // Show success message
        if (successMessage) {
            showToastFn('success', successMessage);
            delete body.dataset.flashSuccess;
        }

        // Show error message
        if (errorMessage) {
            showToastFn('error', errorMessage);
            delete body.dataset.flashError;
        }

        // Show validation errors
        if (errorsJson) {
            try {
                const errors = JSON.parse(errorsJson);
                if (Array.isArray(errors) && errors.length > 0) {
                    errors.forEach(function(error) {
                        showToastFn('error', error);
                    });
                }
                delete body.dataset.flashErrors;
            } catch (e) {
                console.error('Error parsing flash errors:', e);
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFlashMessages);
    } else {
        initFlashMessages();
    }
})();

