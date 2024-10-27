import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import collectModuleAssetsPaths from './vite-module-loader.js';
import inject from "@rollup/plugin-inject";

const allPaths = [
    'resources/css/app.scss',
    'resources/js/app.js',
];

const paths = await collectModuleAssetsPaths(allPaths,'Modules');

export default defineConfig({
    build: {
        sourcemap: true,
        rollupOptions: {
            output: {
                chunkFileNames: 'js/[name].js',
                entryFileNames: 'js/[name].js',
            },
        }
    },
    plugins: [
        inject({   // => that should be first under plugins array
            $: 'jquery',
            jQuery: 'jquery',
        }),
        laravel({
            input: paths,
            refresh: true
        }),
    ],
    css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler' // or "modern"
      }
    }
  }
});
