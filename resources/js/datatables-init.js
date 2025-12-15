/**
 * DataTables Initialization
 * Ensures jQuery and DataTables are properly loaded
 */

import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-bs5';

// Ensure jQuery is available globally
if (typeof window.$ === 'undefined') {
    window.$ = window.jQuery = $;
}

// Initialize DataTables when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // DataTables will auto-initialize via Yajra DataTables scripts
    // This file ensures jQuery and DataTables are loaded before scripts run
    console.log('DataTables initialized - jQuery available:', typeof window.$ !== 'undefined');
});

