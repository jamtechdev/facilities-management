import './bootstrap';
import $ from 'jquery';
import 'bootstrap';
import 'datatables.net';
import 'datatables.net-bs5';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Make jQuery globally available BEFORE anything else
window.$ = window.jQuery = $;

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
    
    switch(type) {
        case 'success':
            toastr.success(message, toastTitle, options);
            break;
        case 'error':
        case 'danger':
            toastr.error(message, toastTitle, options);
            break;
        case 'warning':
            toastr.warning(message, toastTitle, options);
            break;
        case 'info':
            toastr.info(message, toastTitle, options);
            break;
        default:
            toastr.info(message, toastTitle, options);
    }
};

// Export for use in other modules if needed
export default $;
