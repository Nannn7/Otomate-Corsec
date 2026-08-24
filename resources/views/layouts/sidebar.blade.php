<div class="hidden fixed top-0 bottom-0 z-20 flex-col items-stretch h-full border-r sidebar bg-agi-50 dark:bg-coal-600 border-r-gray-200 dark:border-r-coal-100 lg:flex shrink-0"
    data-drawer="true" data-drawer-class="top-0 bottom-0 drawer drawer-start" data-drawer-enable="true|lg:false"
    id="sidebar">
    <div class="hidden relative justify-between items-center px-3 sidebar-header lg:flex lg:px-6 shrink-0"
        id="sidebar_header">
        <a class="dark:hidden" href="{{ url('/') }}">
            <img class="default-logo min-h-[22px] max-w-none" style="height: 53.55px;"
                src="assets/media/app/logo-agi-croped.png" />
            <img class="small-logo min-h-[22px] max-w-none" style="height: 50px"
                src="assets/media/app/logo-agi-mini.png" />
        </a>
        <a class="light:hidden dark:flex" href="{{ url('/') }}">
            <img class="default-logo min-h-[22px] max-w-none" style="height: 53.55px;"
                src="assets/media/app/logo-agi-croped.png" />
            <img class="small-logo min-h-[22px] max-w-none" style="height: 50px"
                src="assets/media/app/logo-agi-mini.png" />
        </a>
        <button
            class="btn btn-icon btn-icon-md size-[30px] rounded-lg border border-gray-200 dark:border-gray-300 bg-light text-gray-500 hover:text-gray-700 toggle absolute left-full top-2/4 -translate-x-2/4 -translate-y-2/4"
            data-toggle="body" data-toggle-class="sidebar-collapse" id="sidebar_toggle">
            <i class="transition-all duration-300 ki-filled ki-black-left-line toggle-active:rotate-180">
            </i>
        </button>
    </div>

    <div class="sidebar-content flex grow shrink-0 py-5 pr-2 h-[100%]" id="sidebar_content">
        <div class="flex pr-1 pl-2 scrollable-y-hover grow shrink-0 lg:pl-5 lg:pr-3" data-scrollable="true"
            data-scrollable-dependencies="#sidebar_header" data-scrollable-height="auto" data-scrollable-offset="10px"
            data-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable"
            style="--tw-scrollbar-thumb-color: var(--tw-primary)">
            <div class="flex flex-col gap-0.5 menu grow" data-menu="true" data-menu-accordion-expand-all="false"
                id="sidebar_menu">
                <a class="menu-item" href="/">
                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px]"
                        tabindex="0">
                        <span class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px]">
                            <i class="text-lg ki-filled ki-element-11 text-primary"></i>
                        </span>
                        <span
                            class="menu-title text-sm font-semibold text-gray-700 menu-item-active:text-primary menu-link-hover:!text-primary">
                            Dashboards
                        </span>
                    </div>
                </a>
                @php
                    // Ensure $menus is defined and is an object
                    $menus = isset($menus) ? json_decode(json_encode($menus)) : new stdClass();

                    // Section 'main' selalu di atas
                    $mainSection = ['main'];

                    // Sections yang harus selalu di akhir dan berurutan
                    $fixedEndSections = ['otorisator', 'laporan', 'master', 'system'];

                    // Ambil semua section yang ada di $menus
                    $allSections = array_keys((array) $menus);

                    // Filter section yang bukan termasuk main dan fixed end sections
                    $dynamicSections = array_diff($allSections, array_merge($mainSection, $fixedEndSections));

                    // Gabungkan: main di atas, section dinamis di tengah, fixed end sections di akhir
                    $sectionOrder = array_merge($mainSection, $dynamicSections, $fixedEndSections);

                    // Default section titles
                    $defaultSectionTitles = [
                        'main' => 'Apps',
                        'otorisator' => 'Otorisator',
                        'laporan' => 'Laporan',
                        'master' => 'Master Data',
                        'system' => 'Systems',
                    ];

                    // A menu is visible if the user has the permission set on
                    // it (matches the checkboxes on the Role edit page).
                    // Falls back to the old hardcoded roles list only if a
                    // menu item has no permission configured.
                    $canSeeMenu = function ($item) {
                        $permission = $item->permission ?? null;
                        if (!empty($permission)) {
                            // Support "a|b" pipe syntax (same convention as route
                            // middleware's permission:a|b) so a menu can show for
        // any one of several permissions, e.g. a read-only
        // "Approval Requests" viewer alongside the approver.
        if (str_contains($permission, '|')) {
            foreach (explode('|', $permission) as $singlePermission) {
                                    if (auth()->user()->can(trim($singlePermission))) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                            return auth()->user()->can($permission);
                        }
                        return auth()
                            ->user()
                            ->hasRole($item->roles ?? []);
                    };

                    // Parent items show if their own permission passes, or if
                    // any of their sub-items would (e.g. "Logs" has no single
                    // permission of its own, but should show when the user can
                    // see either "System Logs" or "Audit Logs").
                    $canSeeMenuOrSubs = function ($item) use ($canSeeMenu) {
                        if ($canSeeMenu($item)) {
                            return true;
                        }
                        if (!empty($item->sub) && is_array($item->sub)) {
                            foreach ($item->sub as $sub) {
                                if ($canSeeMenu($sub)) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    };
                @endphp

                @foreach ($sectionOrder as $section)
                    @if (!empty($menus->$section))
                        @php
                            $hasVisibleItems = false;
                            foreach ($menus->$section as $menu) {
                                if ($canSeeMenuOrSubs($menu)) {
                                    $hasVisibleItems = true;
                                    break;
                                }
                            }
                        @endphp

                        @if ($hasVisibleItems)
                            <div class="pb-px menu-item pt-2.25">
                                <span
                                    class="menu-heading uppercase text-2sm font-semibold text-gray-500 pl-[10px] pr-[10px]">
                                    {{ $defaultSectionTitles[$section] ?? ucfirst($section) }}
                                </span>
                            </div>

                            @foreach ($menus->$section as $menu)
                                @if ($canSeeMenuOrSubs($menu))
                                    @if (isset($menu->sub))
                                        <div class="menu-item {{ request()->routeIs($menu->path) || request()->routeIs($menu->path . '.*') ? 'show' : '' }}"
                                            data-menu-item-toggle="accordion" data-menu-item-trigger="click">
                                            <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px]"
                                                tabindex="0">
                                                <span
                                                    class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px]">
                                                    <i
                                                        class="{{ $menu->icon ?? 'ki-filled ki-element-11 text-lg' }}"></i>
                                                </span>
                                                <span
                                                    class="menu-title text-sm font-semibold text-gray-700 menu-item-active:text-primary menu-link-hover:!text-primary">
                                                    @php
                                                        $title = $menu->title;
                                                        if (strlen($title) > 30) {
                                                            $words = explode(' ', $title);
                                                            $lines = [];
                                                            $currentLine = '';

                                                            foreach ($words as $word) {
                                                                if (
                                                                    strlen($currentLine . ' ' . $word) <= 30 ||
                                                                    empty($currentLine)
                                                                ) {
                                                                    $currentLine = empty($currentLine)
                                                                        ? $word
                                                                        : $currentLine . ' ' . $word;
                                                                } else {
                                                                    $lines[] = $currentLine;
                                                                    $currentLine = $word;
                                                                }
                                                            }

                                                            if (!empty($currentLine)) {
                                                                $lines[] = $currentLine;
                                                            }

                                                            echo implode('<br>', $lines);
                                                        } else {
                                                            echo $title;
                                                        }
                                                    @endphp
                                                </span>
                                                <span
                                                    class="menu-arrow text-gray-400 w-[20px] shrink-0 justify-end ml-1 mr-[-10px]">
                                                    <i class="ki-filled ki-plus text-2xs menu-item-show:hidden">
                                                    </i>
                                                    <i
                                                        class="hidden ki-filled ki-minus text-2xs menu-item-show:inline-flex">
                                                    </i>
                                                </span>
                                            </div>
                                            @if (is_array($menu->sub))
                                                <div
                                                    class="menu-accordion gap-0.5 pl-[10px] relative before:absolute before:left-[20px] before:top-0 before:bottom-0 before:border-l before:border-gray-200">
                                                    @foreach ($menu->sub as $sub)
                                                        @if ($canSeeMenu($sub))
                                                            <div
                                                                class="menu-item {{ request()->routeIs($sub->path . '.*') && in_array(request()->route()->getName(), [$sub->path . '.index', $sub->path . '.create', $sub->path . '.edit', $sub->path . '.restore']) ? 'active' : '' }}">
                                                                <a class="menu-link gap-[14px] pl-[10px] pr-[10px] py-[8px] border border-transparent items-center grow menu-item-active:bg-secondary-active dark:menu-item-active:bg-coal-300 dark:menu-item-active:border-gray-100 menu-item-active:rounded-lg hover:bg-secondary-active dark:hover:bg-coal-300 dark:hover:border-gray-100 hover:rounded-lg"
                                                                    href="{{ $sub->path ? route($sub->path . '.index') : '' }}"
                                                                    tabindex="0">
                                                                    <span
                                                                        class="menu-bullet flex w-[6px] relative before:absolute before:top-0 before:size-[6px] before:rounded-full before:-translate-x-1/2 before:-translate-y-1/2 menu-item-active:before:bg-primary menu-item-hover:before:bg-primary">
                                                                    </span>
                                                                    <span
                                                                        class="menu-title text-2sm font-medium text-gray-700 menu-item-active:text-primary menu-item-active:font-semibold menu-link-hover:!text-primary">
                                                                        {{ $sub->title }}
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div
                                            class="menu-item {{ request()->routeIs($menu->path . '.*') ? 'active' : '' }}">
                                            <a class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px]"
                                                href="{{ $menu->path ? route($menu->path . '.index') : '' }}">
                                                <span
                                                    class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px] menu-item-active:text-primary menu-link-hover:!text-primary">
                                                    <i
                                                        class="{{ $menu->icon ?? 'ki-filled ki-element-11 text-lg' }}"></i>
                                                </span>
                                                <span
                                                    class="menu-title text-sm font-semibold text-gray-700 menu-item-active:text-primary menu-link-hover:!text-primary">
                                                    @php
                                                        $title = $menu->title;
                                                        if (strlen($title) > 30) {
                                                            $words = explode(' ', $title);
                                                            $lines = [];
                                                            $currentLine = '';

                                                            foreach ($words as $word) {
                                                                if (
                                                                    strlen($currentLine . ' ' . $word) <= 30 ||
                                                                    empty($currentLine)
                                                                ) {
                                                                    $currentLine = empty($currentLine)
                                                                        ? $word
                                                                        : $currentLine . ' ' . $word;
                                                                } else {
                                                                    $lines[] = $currentLine;
                                                                    $currentLine = $word;
                                                                }
                                                            }

                                                            if (!empty($currentLine)) {
                                                                $lines[] = $currentLine;
                                                            }

                                                            echo implode('<br>', $lines);
                                                        } else {
                                                            echo $title;
                                                        }
                                                    @endphp
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
