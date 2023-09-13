import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/shop.scss',
                'resources/sass/admin.scss',
                'resources/js/shop.js',
                'resources/js/admin.js',
                "resources/js/ckeditor-classic.js",
                "resources/js/ckeditor-inline.js",
                "resources/js/ckeditor-balloon.js",
                "resources/js/ckeditor-balloon-block.js",
                "resources/js/ckeditor-document.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '$': 'jQuery'
        }
    },
});
