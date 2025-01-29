import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue'
import liveReload from 'vite-plugin-live-reload'
import tailwindcss from '@tailwindcss/vite'
import glob from 'fast-glob'
import path from 'path'

const assetDirs = [
    'components/contrib/*/assets/',
    'components/custom/*/assets/',
    'core/assets/',
    'modules/contrib/*/assets/',
    'modules/custom/*/assets/',
    'core/modules/*/assets/'
];

// Find all files matching extensions in the directories
const input = Object.fromEntries(
    glob.sync(
        assetDirs.map(dir => path.resolve(__dirname, dir, '**/*.{js,ts,scss,css}'))
    ).map(file => [
        path.relative(path.resolve(__dirname), file).replace(/\.[^.]+$/, ''), // Name
        file // Full path
    ])
);

export default defineConfig({
    root: 'core/resources',
    plugins: [
        vue(),
        liveReload([
            __dirname + '/core/modules/*/templates/**/*.html.twig',
            __dirname + '/modules/custom/*/templates/**/*.html.twig',
            __dirname + '/modules/contrib/*/templates/**/*.html.twig',
            __dirname + '/core/templates/**/*.html.twig',
            __dirname + '/core/components/*/templates/**/*.html.twig',
            __dirname + '/components/contrib/*/templates/**/*.html.twig',
            __dirname + '/components/custom/*/templates/**/*.html.twig',
        ]),
        tailwindcss(),
    ],
    build: {
        outDir: 'assets',
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
            "@assets": path.resolve(__dirname, "core/assets/"),
            "@modules": path.resolve(__dirname, "core/modules/"),
            "@components": path.resolve(__dirname, "core/components/"),
        },
    },
})
