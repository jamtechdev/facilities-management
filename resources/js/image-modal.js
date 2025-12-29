/**
 * Image Modal Handler
 * Handles opening images in modal dialogs
 */

(function() {
    'use strict';

    /**
     * Open image in modal
     * @param {string} imageSrc - Image source URL
     * @param {string} imageType - Type of image (before/after/etc)
     */
    window.openImageModal = function(imageSrc, imageType) {
        // Check if Bootstrap modal is available
        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            // Fallback: open in new window
            window.open(imageSrc, '_blank');
            return;
        }

        // Get or create modal element
        let modalElement = document.getElementById('imageModal');
        
        if (!modalElement) {
            // Create modal structure
            modalElement = document.createElement('div');
            modalElement.id = 'imageModal';
            modalElement.className = 'modal fade';
            modalElement.setAttribute('tabindex', '-1');
            modalElement.setAttribute('aria-labelledby', 'imageModalLabel');
            modalElement.setAttribute('aria-hidden', 'true');
            
            modalElement.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">Job Photo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img id="modalImage" src="" alt="Job Photo" class="img-fluid modal-image">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modalElement);
        }

        // Set image source and type
        const modalImage = modalElement.querySelector('#modalImage');
        const modalTitle = modalElement.querySelector('#imageModalLabel');
        
        if (modalImage) {
            modalImage.src = imageSrc;
            modalImage.alt = imageType ? `${imageType} Photo` : 'Job Photo';
            
            // Add error handler for broken images in modal
            modalImage.onerror = function() {
                if (window.handleImageError) {
                    window.handleImageError({ target: this });
                } else {
                    // Fallback if handler not loaded
                    this.src = '/Image-not-found.png';
                    this.alt = 'Image not found';
                }
            };
        }
        
        if (modalTitle && imageType) {
            modalTitle.textContent = `${imageType.charAt(0).toUpperCase() + imageType.slice(1)} Photo`;
        }

        // Show modal
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    };

    /**
     * Initialize image click handlers
     */
    function initImageHandlers() {
        // Handle all images with onclick attributes (legacy support)
        document.querySelectorAll('img[onclick*="openImageModal"]').forEach(function(img) {
            const onclickAttr = img.getAttribute('onclick');
            const match = onclickAttr.match(/openImageModal\(['"]([^'"]+)['"]\s*,\s*['"]([^'"]+)['"]\)/);
            
            if (match) {
                img.removeAttribute('onclick');
                img.style.cursor = 'pointer';
                img.addEventListener('click', function() {
                    window.openImageModal(match[1], match[2]);
                });
            }
        });

        // Handle images with data attributes (modern approach)
        document.querySelectorAll('[data-image-modal]').forEach(function(element) {
            const imageSrc = element.dataset.imageModal || element.src || element.href;
            const imageType = element.dataset.imageType || '';
            
            element.style.cursor = 'pointer';
            element.addEventListener('click', function(e) {
                e.preventDefault();
                window.openImageModal(imageSrc, imageType);
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initImageHandlers);
    } else {
        initImageHandlers();
    }
})();

