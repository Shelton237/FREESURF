import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';
const disablePWA = process.env.SKIP_PWA === '1';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            disable: disablePWA,
            registerType: 'autoUpdate',
            includeAssets: ['logo.png', 'logo_black.png', 'favicon.ico'],
            manifest: {
                name: 'CuWiP',
                short_name: 'CuWiP',
                start_url: '/portal',
                display: 'standalone',
                background_color: '#ffffff',
                theme_color: '#dc2626',
                icons: [
                    { src: 'logo.png', sizes: '192x192', type: 'image/png' },
                    { src: 'logo.png', sizes: '512x512', type: 'image/png' },
                ],
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg}'],
                runtimeCaching: [
                    {
                        urlPattern: /\/portal(\/.*)?/,
                        handler: 'NetworkFirst',
                        options: { cacheName: 'portal-pages' },
                    },
                    {
                        urlPattern: /\/backoffice(\/.*)?/,
                        handler: 'NetworkFirst',
                        options: { cacheName: 'backoffice-pages' },
                    },
                    {
                        urlPattern: /\/storage\//,
                        handler: 'CacheFirst',
                        options: { cacheName: 'storage-assets' },
                    },
                ],
            },
        }),
    ],
});
