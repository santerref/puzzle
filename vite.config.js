import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import glob from 'fast-glob'
import path from 'path'

const assetDirs = [
    'core/assets/',
    'core/modules/*/assets/',
    'core/components/*/assets/',
];

// Find all files matching extensions in the directories
const input = Object.fromEntries(
    glob.sync(
        assetDirs.map(dir => path.resolve(__dirname, dir, '**/*.{js,ts,css,scss}'))
    ).map(file => [
        path.relative(path.resolve(__dirname), file)
            .replace(/\.[^.]+$/, '')
            .replace(/\/assets\//, '/')
            .replace(/core\/modules\//, 'modules/')
            .replace(/core\/components\//, 'components/'),
        file // Full path
    ])
);

console.log(input)

export default defineConfig({
    root: 'public',
    plugins: [
        vue(),
        tailwindcss(),
    ],
    publicDir: false,
    build: {
        outDir: 'static',
        assetsDir: '.',
        rollupOptions: {
            input
        },
        manifest: 'manifest.json'
    },
    server: {
        cors: true,
        fs: {
            allow: [
                path.resolve(__dirname, "core/assets/"),
                path.resolve(__dirname, "public/static/")
            ]
        }
    },
    resolve: {
        alias: {
            "@assets": path.resolve(__dirname, "core/assets/"),
            "@modules": path.resolve(__dirname, "core/modules/"),
            "@components": path.resolve(__dirname, "core/components/"),
        },
    },
})
