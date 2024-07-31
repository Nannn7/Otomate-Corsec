import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import collectModuleAssetsPaths from './vite-module-loader.js';

const paths = [
    'resources/css/app.scss',
    'resources/js/app.js',
];
const allPaths = await collectModuleAssetsPaths(paths, 'Modules');

export default defineConfig({
    plugins: [
        laravel({
            input: allPaths,
            refresh: true,
        }),
    ],
});
