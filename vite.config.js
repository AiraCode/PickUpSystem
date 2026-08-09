import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-api.js', 'resources/js/user-api.js', 'resources/img/Logo_user-removebg-preview.png', 'resources/img/logo_admin1-removebg-preview.png', 'resources/img/logo_admin2-removebg-preview.png', 'resources/img/img_aki.png'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        allowedHosts: ['.ngrok-free.app', '.ngrok-free.dev'],
        hmr: {
            // Masukkan domain ngrok kamu tanpa https://
            host: 'noninclusive-donna-pseudoparallel.ngrok-free.dev',
            protocol: 'wss',
            clientPort: 443,
        },
    },
});
