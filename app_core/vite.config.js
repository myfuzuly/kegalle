import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            buildDirectory: 'vite-dist',
            refresh: false,
        }),
    ],
    build: {
        // Target modern browsers for better tree-shaking and minification
        target: 'es2018',
        // Use lightningcss for best-in-class CSS minification (Vite 8 built-in)
        cssMinify: 'lightningcss',
        // Inline assets < 4 KB as data URIs (saves HTTP requests)
        assetsInlineLimit: 4096,
        // Skip reporting gzip size (speeds up build)
        reportCompressedSize: false,
        // Warn on chunks > 500 KB
        chunkSizeWarningLimit: 500,
        rollupOptions: {
            output: {
                entryFileNames: 'js/[name]-[hash].js',
                chunkFileNames: 'js/[name]-[hash].js',
                assetFileNames: ({ name }) => {
                    if (/\.(css)$/.test(name ?? '')) return 'css/[name]-[hash][extname]'
                    return 'assets/[name]-[hash][extname]'
                },
            },
        },
    },
})
