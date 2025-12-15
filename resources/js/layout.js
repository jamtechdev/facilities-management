/**
 * Layout JavaScript
 * Handles sidebar, navbar, and general layout interactions
 */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initSidebarToggle();
        initBootstrapTooltips();
        initNavbarScrollEffect();
    });

    /**
     * Mobile Sidebar Toggle
     */
    function initSidebarToggle() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar-modern');
        
        if (!sidebarToggle || !sidebar) return;

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            
            // Add overlay for mobile
            if (sidebar.classList.contains('mobile-open')) {
                createMobileOverlay();
            } else {
                removeMobileOverlay();
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 992) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                    removeMobileOverlay();
                }
            }
        });

        // Close sidebar on window resize if desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('mobile-open');
                removeMobileOverlay();
            }
        });
    }

    /**
     * Create mobile overlay
     */
    function createMobileOverlay() {
        if (document.getElementById('mobile-overlay')) return;
        
        const overlay = document.createElement('div');
        overlay.id = 'mobile-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            transition: opacity 0.3s ease;
        `;
        
        overlay.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar-modern');
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
            }
            removeMobileOverlay();
        });
        
        document.body.appendChild(overlay);
        
        // Fade in
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
    }

    /**
     * Remove mobile overlay
     */
    function removeMobileOverlay() {
        const overlay = document.getElementById('mobile-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 300);
        }
    }

    /**
     * Initialize Bootstrap Tooltips
     */
    function initBootstrapTooltips() {
        if (typeof bootstrap === 'undefined') return;
        
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Navbar Scroll Effect
     */
    function initNavbarScrollEffect() {
        const navbar = document.querySelector('.navbar-top');
        if (!navbar) return;

        let lastScroll = 0;
        let ticking = false;

        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const currentScroll = window.pageYOffset;
                    
                    if (currentScroll <= 0) {
                        navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.05)';
                    } else if (currentScroll > lastScroll && currentScroll > 100) {
                        // Scrolling down
                        navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
                    } else {
                        // Scrolling up
                        navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.05)';
                    }
                    
                    lastScroll = currentScroll;
                    ticking = false;
                });
                
                ticking = true;
            }
        });
    }

})();


