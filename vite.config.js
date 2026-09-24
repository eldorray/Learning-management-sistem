import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    esbuild: { jsx: 'automatic' },
    resolve: {
        alias: [
            { find: '@designcodeio/threeui/style.css', replacement: new URL('./resources/threeui/src/shaders/threeui.css', import.meta.url).pathname },
            { find: '@designcodeio/threeui', replacement: new URL('./resources/js/threeui-kage.tsx', import.meta.url).pathname },
        ],
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.tsx'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
