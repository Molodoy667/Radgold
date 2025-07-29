import { defineConfig } from 'vite';

import laravel, { refreshPaths } from 'laravel-vite-plugin';

import vue from '@vitejs/plugin-vue2';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/backend/app.css',
                'resources/frontend/js/app.js',
                'resources/frontend/css/app.css',
                'resources/frontend/js/filepond.js',
                'resources/frontend/js/vue-config.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    },
  build: {
    chunkSizeWarningLimit: 5000,
  },
});
