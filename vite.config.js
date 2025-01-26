import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue'
import liveReload from 'vite-plugin-live-reload'
import {resolve} from 'node:path'
import tailwindcss from '@tailwindcss/vite'
import path from 'path';

export default defineConfig({
    root: 'core/resources',
    plugins: [
        vue(),
        liveReload([
            __dirname + '/core/resources/templates/**/*.html.twig',
        ]),
        tailwindcss(),
    ],
    build: {
        outDir: 'assets',
        rollupOptions: {
            input: resolve(__dirname, 'core/resources/js/puzzle.js')
        },
        manifest: 'manifest.json'
    },
    server: {
        cors: true
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "core/resources/"),
        },
    },
})
