import './bootstrap';
import $ from 'jquery';
import * as bootstrap from 'bootstrap';
import 'datatables.net';
import 'datatables.net-bs5';
// Toastr requires jQuery to be available globally first
import toastr from 'toastr';
// Toastr CSS is imported in app.css to ensure proper loading
import './image-error-handler';

// Make jQuery globally available BEFORE anything else
window.$ = window.jQuery = $;

// Make Bootstrap globally available
window.bootstrap = bootstrap;

// Ensure jQuery is available immediately
if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
    window.$ = window.jQuery = $;
}

// Configure Toastr globally
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    preventDuplicates: false,
    onclick: null,
    showDuration: '600',        // Slower fade in (600ms)
    hideDuration: '1000',       // Smooth fade out (1 second)
    timeOut: '8000',            // Show for 8 seconds (increased from 5)
    extendedTimeOut: '2000',    // Extended time on hover (2 seconds)
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut',
    tapToDismiss: true          // Allow tap to dismiss
};

// Make toastr globally available
window.toastr = toastr;

// Global helper function for consistent toast notifications
window.showToast = function(type, message, title = '', customOptions = {}) {
    // Ensure toastr is available
    if (typeof window.toastr === 'undefined' && typeof toastr === 'undefined') {
        console.warn('Toastr not loaded, falling back to alert');
        alert(message);
        return;
    }
    
    const toastrInstance = window.toastr || toastr;
    
    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Information'
    };
    
    const toastTitle = title || titles[type] || '';
    
    // Different timeout durations based on type
    const timeoutOptions = {
        success: 6000,   // 6 seconds for success messages
        error: 10000,    // 10 seconds for errors (users need more time to read)
        warning: 8000,   // 8 seconds for warnings
        info: 7000       // 7 seconds for info messages
    };
    
    // Merge custom options with default timeout
    const options = {
        timeOut: timeoutOptions[type] || 8000,
        ...customOptions
    };
    
    try {
        switch(type) {
            case 'success':
                toastrInstance.success(message, toastTitle, options);
                break;
            case 'error':
            case 'danger':
                toastrInstance.error(message, toastTitle, options);
                break;
            case 'warning':
                toastrInstance.warning(message, toastTitle, options);
                break;
            case 'info':
                toastrInstance.info(message, toastTitle, options);
                break;
            default:
                toastrInstance.info(message, toastTitle, options);
        }
    } catch (e) {
        console.error('Error showing toast:', e);
        // Fallback to alert if toastr fails
        alert(message);
    }
};

// Export for use in other modules if needed
export default $;
