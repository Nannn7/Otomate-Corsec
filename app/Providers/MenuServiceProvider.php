<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    protected array $menus = [];

    public function boot(): void
    {
        $this->menus = $this->loadMenus();
        $this->shareMenusWithViews();
    }

    protected function loadMenus(): array
    {
        $moduleJsonFiles = $this->moduleJsonFiles();
        $signature = $this->menuSignature($moduleJsonFiles);
        $cacheKey = 'app.menus';

        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ($cached['signature'] ?? null) === $signature) {
            return $cached['menus'] ?? [];
        }

        $menus = $this->buildMenus($moduleJsonFiles);

        Cache::forever($cacheKey, [
            'signature' => $signature,
            'menus' => $menus,
        ]);

        return $menus;
    }

    protected function moduleJsonFiles(): array
    {
        $modulesPath = base_path('Modules');
        if (!File::isDirectory($modulesPath)) {
            return [];
        }

        $files = [];

        foreach (File::directories($modulesPath) as $moduleDir) {
            $moduleJsonPath = $moduleDir . DIRECTORY_SEPARATOR . 'module.json';
            if (File::exists($moduleJsonPath)) {
                $files[] = $moduleJsonPath;
            }
        }

        sort($files);

        return $files;
    }

    protected function menuSignature(array $moduleJsonFiles): string
    {
        $signature = [];

        foreach ($moduleJsonFiles as $moduleJsonFile) {
            $signature[] = [
                'path' => str_replace('\\', '/', $moduleJsonFile),
                'last_modified' => File::lastModified($moduleJsonFile),
            ];
        }

        return md5(json_encode($signature));
    }

    protected function buildMenus(array $moduleJsonFiles): array
    {
        $menus = [];

        foreach ($moduleJsonFiles as $moduleJsonPath) {
            $moduleConfig = json_decode(File::get($moduleJsonPath), true);
            if (!isset($moduleConfig['menu']) || !is_array($moduleConfig['menu'])) {
                continue;
            }

            $this->mergeMenusInto($menus, $moduleConfig['menu']);
        }

        return $menus;
    }

    protected function mergeMenusInto(array &$menus, array $menu): void
    {
        foreach ($menu as $key => $items) {
            if (!isset($menus[$key])) {
                $menus[$key] = [];
            }

            foreach ($items as $newItem) {
                $existingItemKey = $this->findExistingMenuItem($menus[$key], $newItem);

                if ($existingItemKey !== null) {
                    $menus[$key][$existingItemKey]['sub'] = array_merge(
                        $menus[$key][$existingItemKey]['sub'] ?? [],
                        $newItem['sub'] ?? []
                    );
                    continue;
                }

                $menus[$key][] = $newItem;
            }
        }
    }

    protected function findExistingMenuItem(array $menuItems, array $newItem): ?int
    {
        foreach ($menuItems as $index => $item) {
            if (($item['title'] ?? null) === ($newItem['title'] ?? null) || ($item['path'] ?? null) === ($newItem['path'] ?? null)) {
                return $index;
            }
        }

        return null;
    }

    protected function shareMenusWithViews(): void
    {
        view()->share('menus', $this->menus);
    }
}
