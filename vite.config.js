import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // CSS Files
                'resources/css/globals.css',
                'resources/css/app.css',
                'resources/css/common-styles.css',
                'resources/css/utilities.css',
                'resources/css/layout.css',
                'resources/css/auth.css',
                'resources/css/guest.css',
                'resources/css/welcome.css',
                'resources/css/forms.css',
                'resources/css/datatables.css',
                'resources/css/profile.css',
                'resources/css/client-dashboard.css',
                'resources/css/staff-dashboard.css',
                'resources/css/admin-dashboard.css',
                'resources/css/timesheet.css',
                'resources/css/entity-details.css',
                'resources/css/clock-widget.css',
                'resources/css/document-gallery.css',
                'resources/css/preloader.css',
                // JavaScript Files
                'resources/js/jquery-global.js',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/auth.js',
                'resources/js/layout.js',
                'resources/js/forms.js',
                'resources/js/flash-messages.js',
                'resources/js/image-modal.js',
                'resources/js/datatables-init.js',
                'resources/js/datatables-handlers.js',
                'resources/js/entity-details.js',
                'resources/js/global-loader.js',
                'resources/js/inline-edit.js',
                'resources/js/preloader.js',
                'resources/js/navbar-collapse-fix.js',
                'resources/js/dashboard-charts.js',
                'resources/js/document-gallery.js',
                'resources/js/firebase-notifications.js',
                // Page-specific JS Files
                'resources/js/pages/users.js',
                'resources/js/pages/payouts.js',
                'resources/js/pages/feedback.js',
                'resources/js/pages/inventory.js',
                'resources/js/pages/communications.js'
            ],
            refresh: true,
        }),
    ],
});
