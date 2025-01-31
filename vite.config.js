import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import glob from 'fast-glob'
import path from 'path'

const assetDirs = [
    'core/modules/*/assets/',
    'core/components/*/assets/',
];

// Find all files matching extensions in the directories
const input = Object.fromEntries(
    glob.sync(
        assetDirs.map(dir => path.resolve(__dirname, dir, '**/*.{js,ts,scss,css}'))
    ).map(file => [
        path.relative(path.resolve(__dirname), file)
            .replace(/\.[^.]+$/, '')
            .replace(/core\/modules\//, 'modules/')
            .replace(/core\/components\//, 'components/'),
        file
    ])
);

export default defineConfig({
    root: '.',
    base: './',
    plugins: [
        vue(),
        tailwindcss()
    ],
    publicDir: false,
    build: {
        outDir: 'public/static',
        assetsDir: '.',
        rollupOptions: {
            input
        },
        manifest: 'manifest.json'
    },
    server: {
        cors: true
    },
    resolve: {
        alias: {
            "@modules": path.resolve(__dirname, "core/modules/"),
            "@components": path.resolve(__dirname, "core/components/"),
        },
    },
})
