/**
 * Global Image Error Handler
 * Handles broken/missing images by showing a placeholder
 */

(function() {
    'use strict';

    // Path to the not-found placeholder image
    const NOT_FOUND_IMAGE = '/Image-not-found.png';

    /**
     * Handle image error - replace with placeholder
     * @param {Event} event - Error event
     */
    function handleImageError(event) {
        const img = event.target;
        
        // Prevent infinite loop if placeholder also fails
        if (img.src && img.src.includes('not-found-image')) {
            return;
        }

        // Store original src for potential retry
        if (!img.dataset.originalSrc) {
            img.dataset.originalSrc = img.src;
        }

        // Replace with placeholder
        img.src = NOT_FOUND_IMAGE;
        img.alt = img.alt || 'Image not found';
        img.classList.add('image-not-found');
        
        // Add title for accessibility
        img.title = 'Image could not be loaded';
    }

    /**
     * Initialize error handlers for all images
     */
    function initImageErrorHandlers() {
        // Handle existing images
        document.querySelectorAll('img').forEach(function(img) {
            if (!img.onerror) {
                img.addEventListener('error', handleImageError);
            }
        });

        // Handle dynamically added images (using MutationObserver)
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            // Check if the added node is an image
                            if (node.tagName === 'IMG') {
                                node.addEventListener('error', handleImageError);
                            }
                            // Check for images within the added node
                            const images = node.querySelectorAll && node.querySelectorAll('img');
                            if (images) {
                                images.forEach(function(img) {
                                    img.addEventListener('error', handleImageError);
                                });
                            }
                        }
                    });
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initImageErrorHandlers);
    } else {
        initImageErrorHandlers();
    }

    // Make function globally available for manual use
    window.handleImageError = handleImageError;

})();

