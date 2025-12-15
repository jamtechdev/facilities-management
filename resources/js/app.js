import './bootstrap';
import $ from 'jquery';
import 'bootstrap';
import 'datatables.net';
import 'datatables.net-bs5';

// Make jQuery globally available BEFORE anything else
window.$ = window.jQuery = $;

// Ensure jQuery is available immediately
if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
    window.$ = window.jQuery = $;
}

// Export for use in other modules if needed
export default $;
