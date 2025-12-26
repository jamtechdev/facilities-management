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
                'resources/js/jquery-global.js',
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
        host: process.env.VITE_HOST || '172.16.32.63',
        port: parseInt(process.env.VITE_PORT || '5173'),
        cors: {
            origin: [`http://172.16.32.63:8000`, 'http://localhost:8000', 'http://127.0.0.1:8000'],
            credentials: true,
        },
        strictPort: false,
        hmr: {
            host: process.env.VITE_HOST || '172.16.32.63',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
