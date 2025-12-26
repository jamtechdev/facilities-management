/**
 * jQuery Global Loader
 * Loads jQuery from npm and makes it globally available on all pages
 * This file is loaded first in layout to ensure jQuery is available before any other scripts
 */
import $ from 'jquery';

// Make jQuery globally available immediately - this ensures $ and jQuery are available on all pages
window.$ = window.jQuery = $;

// Verify jQuery is loaded
if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
    console.error('jQuery failed to load from npm');
} else {
    console.log('jQuery loaded successfully and available globally');
}

// Export for module usage
export default $;

