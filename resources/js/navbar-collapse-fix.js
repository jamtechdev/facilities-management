/**
 * Navbar Collapse Fix
 * Fixes Bootstrap collapse margin/padding issues on mobile
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Fix navbar collapse margins
        const navbarCollapse = document.querySelector('.navbar-collapse');

        if (navbarCollapse) {
            // Remove any default margins/padding
            navbarCollapse.style.margin = '0';
            navbarCollapse.style.padding = '0';

            // Listen for Bootstrap collapse events
            navbarCollapse.addEventListener('show.bs.collapse', function() {
                this.style.margin = '0';
                this.style.padding = '0';

                // Add proper spacing only on mobile
                if (window.innerWidth <= 991.98) {
                    this.style.marginTop = '1rem';
                    this.style.paddingTop = '1rem';
                }
            });

            navbarCollapse.addEventListener('shown.bs.collapse', function() {
                this.style.margin = '0';
                this.style.padding = '0';

                if (window.innerWidth <= 991.98) {
                    this.style.marginTop = '1rem';
                    this.style.paddingTop = '1rem';
                }
            });

            navbarCollapse.addEventListener('hide.bs.collapse', function() {
                this.style.margin = '0';
                this.style.padding = '0';
            });

            navbarCollapse.addEventListener('hidden.bs.collapse', function() {
                this.style.margin = '0';
                this.style.padding = '0';
            });

            // Fix on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991.98) {
                    navbarCollapse.style.margin = '0';
                    navbarCollapse.style.padding = '0';
                    navbarCollapse.style.marginTop = '0';
                    navbarCollapse.style.paddingTop = '0';
                }
            });
        }

        // Fix nav items and links
        const navItems = document.querySelectorAll('.navbar-nav .nav-item');
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

        navItems.forEach(function(item) {
            item.style.margin = '0';
            item.style.padding = '0';
        });

        navLinks.forEach(function(link) {
            if (window.innerWidth <= 991.98) {
                link.style.margin = '0.25rem 0';
            } else {
                link.style.margin = '0 0.25rem';
            }
        });

        // Update on resize
        window.addEventListener('resize', function() {
            navLinks.forEach(function(link) {
                if (window.innerWidth <= 991.98) {
                    link.style.margin = '0.25rem 0';
                } else {
                    link.style.margin = '0 0.25rem';
                }
            });
        });
    });
})();
