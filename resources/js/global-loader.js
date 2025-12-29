/**
 * Global Loader Utility
 * Provides a global loading overlay for long-running operations
 * Ensures only one loader is active at a time
 */

(function() {
    'use strict';

    // Create global loader element
    let loaderElement = null;
    let isLoaderActive = false;
    let isPreloaderActive = true; // Assume preloader is active on page load

    /**
     * Check if preloader is still visible
     */
    function isPreloaderVisible() {
        const preloader = document.getElementById('preloader');
        if (!preloader) return false;

        const isHidden = preloader.classList.contains('hide') ||
                        preloader.style.display === 'none' ||
                        window.getComputedStyle(preloader).display === 'none';

        return !isHidden;
    }

    /**
     * Create loader element if it doesn't exist
     */
    function createLoader() {
        if (loaderElement) return;

        loaderElement = document.createElement('div');
        loaderElement.id = 'globalLoader';
        loaderElement.className = 'global-loader';
        loaderElement.innerHTML = `
            <div class="global-loader-overlay"></div>
            <div class="global-loader-content">
                <div class="global-loader-spinner"></div>
                <div class="global-loader-message">Processing...</div>
            </div>
        `;
        document.body.appendChild(loaderElement);
    }

    /**
     * Show global loader
     * @param {string} message - Optional message to display
     */
    window.showGlobalLoader = function(message = 'Processing...') {
        // Don't show if preloader is still visible
        if (isPreloaderVisible()) {
            // Wait a bit and try again
            setTimeout(function() {
                if (!isPreloaderVisible() && !isLoaderActive) {
                    window.showGlobalLoader(message);
                }
            }, 100);
            return;
        }

        // Don't show if already active
        if (isLoaderActive) {
            return;
        }

        createLoader();
        isLoaderActive = true;

        const messageEl = loaderElement.querySelector('.global-loader-message');
        if (messageEl) {
            messageEl.textContent = message;
        }
        loaderElement.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    };

    /**
     * Hide global loader
     */
    window.hideGlobalLoader = function() {
        if (loaderElement && isLoaderActive) {
            loaderElement.classList.remove('show');
            document.body.style.overflow = ''; // Restore scrolling
            isLoaderActive = false;
        }
    };

    // Monitor preloader state
    function checkPreloaderState() {
        isPreloaderActive = isPreloaderVisible();
    }

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            createLoader();
            // Check preloader state periodically
            setInterval(checkPreloaderState, 100);
        });
    } else {
        createLoader();
        // Check preloader state periodically
        setInterval(checkPreloaderState, 100);
    }

    // After page fully loads, mark preloader as inactive
    window.addEventListener('load', function() {
        setTimeout(function() {
            isPreloaderActive = false;
        }, 1000); // Give preloader time to hide
    });
})();
