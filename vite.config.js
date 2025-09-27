import { fileURLToPath, URL } from "url";
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import inject from '@rollup/plugin-inject';
import vue from '@vitejs/plugin-vue';
import path from "path";

import Icons from 'unplugin-icons/vite'
import IconsResolver from 'unplugin-icons/resolver'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'

export default defineConfig({
    build: {
        commonjsOptions: {
            include: ["tailwind.config.js", "node_modules/**"],
        },
        chunkSizeWarningLimit: 1200,
        rollupOptions: {
            output:{
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                }
            }
        }
    },
    optimizeDeps: {
        include: ["tailwind-config"],
    },
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: [
                //Theme - Nordihome
                'resources/sass/nordihome.scss',
                'resources/js/nordihome.js',
                //Theme - NB Russia
                'resources/sass/nbrussia.scss',
                'resources/js/nbrussia.js',

               // 'resources/sass/admin.scss',
                'resources/js/admin.js',
                'resources/css/admin.css',
                'resources/images/logo.svg',
                'resources/css/livewire/notification.css',

                "resources/js/components/widget.js",
                //Vue
                'resources/sass/app.scss',
                'resources/js/app.js',

            ],
            refresh: true,
        }),

        inject({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',

        }),
        AutoImport({
            resolvers: [
                ElementPlusResolver(),
                IconsResolver({
                    prefix: 'Icon',
                }),
            ],
        }),
        Components({
            resolvers: [
                ElementPlusResolver(),
                IconsResolver({
                    enabledCollections: ['ep'],
                }),
            ],

        }),
        Icons({
            autoInstall: true,
        }),
    ],
    resolve: {
        alias: [
            {find: '@', replacement: path.resolve('resources/js') },
            {find: '@Page', replacement: path.resolve('resources/js/Pages') },
            {find: '@Comp', replacement: path.resolve('resources/js/VueComponents') },
            {find: '@Res', replacement: path.resolve('resources/js/Resources') },
            {find: 'tailwind-config.js', replacement: path.resolve(__dirname, "./tailwind.config.js") },
            {find: 'ziggy-js', replacement: path.resolve('vendor/tightenco/ziggy'), },

        ],
    },
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler' // or "modern"
            }
        }
    },

        publicDir: 'public/build',
});
