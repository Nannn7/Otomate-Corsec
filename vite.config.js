import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import collectModuleAssetsPaths from './vite-module-loader.js';
import inject from "@rollup/plugin-inject";

const allPaths = [
    'resources/css/app.scss',
    'resources/js/app.js',
];

const paths = await collectModuleAssetsPaths(allPaths, 'Modules', process.cwd());

// IMPORTANT: every module ships a file literally named "app.js" (and
// "app.scss"). When `input` is a plain array, Rollup derives each entry
// chunk's [name] from that shared basename, so with a static
// `entryFileNames: 'js/[name].js'` pattern they all collide and only one
// module's JS actually survives the build (the others silently disappear
// from manifest.json and never get loaded by @vite(Module::getAssets())).
//
// Passing `input` as an object keyed by a unique, module-prefixed name
// forces Rollup to use that key for [name], so every module's JS/CSS gets
// its own output file.
const input = {};
for (const p of paths) {
    const parts = p.split('/');
    const prefix = parts[0] === 'Modules' ? parts[1].toLowerCase() : 'app';
    const isStyle = /\.(scss|css)$/.test(p);
    const key = isStyle ? `${prefix}-css` : prefix;
    input[key] = p;
}

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
            input,
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
