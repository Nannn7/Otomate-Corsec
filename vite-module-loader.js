import fs from 'fs/promises';
import path from 'path';
import { pathToFileURL } from 'url';

// rootDir is passed in explicitly by vite.config.js (as process.cwd()),
// rather than computed inside this file via __dirname / import.meta.url /
// a default parameter. Vite bundles vite.config.js together with its
// imports into a single temp file before running it, and that bundling
// step has repeatedly made any "where am I on disk" self-reference
// computed *inside this file* unreliable. process.cwd() evaluated at the
// vite.config.js call site does not have that problem.
async function collectModuleAssetsPaths(paths, modulesPath, rootDir) {
    if (typeof rootDir !== 'string' || rootDir.length === 0) {
        throw new Error(
            `[vite-module-loader] rootDir must be a non-empty string (pass process.cwd() from vite.config.js). Got: ${JSON.stringify(rootDir)}`
        );
    }

    if (typeof modulesPath !== 'string' || modulesPath.length === 0) {
        throw new Error(
            `[vite-module-loader] modulesPath must be a non-empty string. Got: ${JSON.stringify(modulesPath)}`
        );
    }

    const modulesDir = path.join(rootDir, modulesPath);
    const moduleStatusesPath = path.join(rootDir, 'modules_statuses.json');

    try {
        // Read module_statuses.json
        const moduleStatusesContent = await fs.readFile(moduleStatusesPath, 'utf-8');
        const moduleStatuses = JSON.parse(moduleStatusesContent);

        // Read module directories
        const moduleDirectories = await fs.readdir(modulesDir);

        for (const moduleDir of moduleDirectories) {
            if (moduleDir === '.DS_Store') {
                // Skip .DS_Store directory
                continue;
            }

            // Check if the module is enabled (status is true)
            if (moduleStatuses[moduleDir] === true) {
                const viteConfigPath = path.join(modulesDir, moduleDir, 'vite.config.js');

                try {
                    await fs.access(viteConfigPath);

                    // IMPORTANT: dynamic import() requires a proper file:// URL.
                    // On Windows, path.join() produces backslash paths
                    // (e.g. "Modules\Usermanagement\vite.config.js"), and passing
                    // that raw string to import() fails with
                    // ERR_UNSUPPORTED_ESM_URL_SCHEME. pathToFileURL() converts it
                    // to a URL that works on every OS.
                    const moduleConfig = await import(pathToFileURL(viteConfigPath).href);

                    if (moduleConfig.paths && Array.isArray(moduleConfig.paths)) {
                        paths.push(...moduleConfig.paths);
                    }
                } catch (error) {
                    // Log instead of swallowing silently — a module being skipped
                    // here (for any reason other than "no vite.config.js") should
                    // be visible in the build output, not invisible.
                    console.warn(`[vite-module-loader] Skipping "${moduleDir}": ${error.message}`);
                }
            }
        }
    } catch (error) {
        console.error(`Error reading module statuses or module configurations: ${error}`);
    }

    return paths;
}

export default collectModuleAssetsPaths;
