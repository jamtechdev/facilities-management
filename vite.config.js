import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/layout.css',
                'resources/css/dashboard.css',
                'resources/css/entity-details.css',
                'resources/css/forms.css',
                'resources/css/datatables.css',
              
                'resources/js/app.js',
                'resources/js/layout.js',
                'resources/js/entity-details.js',
                'resources/js/forms.js',
                'resources/js/datatables-init.js',
                'resources/js/datatables-handlers.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
