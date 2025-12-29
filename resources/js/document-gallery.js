/**
 * Document Gallery JavaScript
 * Handles gallery interactions and filtering
 */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initDocumentGallery();
    });

    function initDocumentGallery() {
        // Add smooth scroll to top when filtering
        const filterLinks = document.querySelectorAll('.sidebar-gallery .list-group-item');
        filterLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Only scroll if it's not the active link
                if (!this.classList.contains('active')) {
                    setTimeout(function() {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }, 100);
                }
            });
        });

        // Handle Enter key on search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const searchValue = this.value;
                    const url = new URL(window.location.href);
                    if (searchValue) {
                        url.searchParams.set('search', searchValue);
                    } else {
                        url.searchParams.delete('search');
                    }
                    url.searchParams.set('page', '1');
                    window.location.href = url.toString();
                }
            });
        }
    }

})();

