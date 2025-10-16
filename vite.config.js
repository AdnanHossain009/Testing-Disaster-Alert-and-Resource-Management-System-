import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/live-dashboard.js',
                'resources/js/push-notifications.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
