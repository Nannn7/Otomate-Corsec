<div
    class="sidebar bg-agi-50 dark:bg-coal-600 border-r border-r-gray-200 dark:border-r-coal-100 fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 h-full"
    data-drawer="true" data-drawer-class="drawer drawer-start top-0 bottom-0" data-drawer-enable="true|lg:false"
    id="sidebar">
    <div class="sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0"
         id="sidebar_header">
        <a class="dark:hidden" href="{{ url('/') }}">
            <img class="default-logo min-h-[22px] max-w-none" style="height: 53.55px;" src="assets/media/app/logo-agi-croped.png" />
            <img class="small-logo min-h-[22px] max-w-none" style="height: 50px" src="assets/media/app/logo-agi-mini.png" />
        </a>
        <a class="light:hidden dark:flex" href="{{ url('/') }}">
            <img class="default-logo min-h-[22px] max-w-none" style="height: 53.55px;" src="assets/media/app/logo-agi-croped.png" />
            <img class="small-logo min-h-[22px] max-w-none" style="height: 50px" src="assets/media/app/logo-agi-mini.png" />
        </a>
        <button
            class="btn btn-icon btn-icon-md size-[30px] rounded-lg border border-gray-200 dark:border-gray-300 bg-light text-gray-500 hover:text-gray-700 toggle absolute left-full top-2/4 -translate-x-2/4 -translate-y-2/4"
            data-toggle="body" data-toggle-class="sidebar-collapse" id="sidebar_toggle">
            <i class="ki-filled ki-black-left-line toggle-active:rotate-180 transition-all duration-300">
            </i>
        </button>
    </div>

    <div class="sidebar-content flex grow shrink-0 py-5 pr-2 h-[100%]" id="sidebar_content">
        <div class="scrollable-y-hover grow shrink-0 flex pl-2 lg:pl-5 pr-1 lg:pr-3" data-scrollable="true"
             data-scrollable-dependencies="#sidebar_header," data-scrollable-height="auto" data-scrollable-offset="0px"
             data-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable" style="--tw-scrollbar-thumb-color: var(--tw-primary)">
            <div class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false"
                 id="sidebar_menu">
                <div class="menu-item" data-menu-item-toggle="accordion" data-menu-item-trigger="click">
                    <div
                        class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px]"
                        tabindex="0">
                        <span class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px]">
                            <i class="ki-filled ki-element-11 text-lg text-primary"></i>
                        </span>
                        <span
                            class="menu-title text-sm font-semibold text-gray-700 menu-item-active:text-primary menu-link-hover:!text-primary">
                            Dashboards
                        </span>
                    </div>
                </div>
                @php
                    $headingOtorisasi = 0;
                    $headingMain = 0;
                    $headingMaster = 0;
                    $headingSystem = 0;

                    // Ensure $menus is defined and is an object
                    $menus = isset($menus) ? json_decode(json_encode($menus)) : new stdClass;

                    // Define the order of sections
                    $sectionOrder = ['main','otorisator', 'master', 'system'];
                @endphp

                @foreach($sectionOrder as $section)
                    @if(!empty($menus->$section))
                        @if($section == 'otorisator' && $headingOtorisasi == 0)
                            <div class="menu-item pt-2.25 pb-px">
                                <span class="menu-heading uppercase text-2sm font-semibold text-gray-500 pl-[10px] pr-[10px]">
                                    Otorisator
                                </span>
                            </div>
                            @php $headingOtorisasi = 1; @endphp
                        @elseif($section == 'main' && $headingMain == 0)
                            <div class="menu-item pt-2.25 pb-px">
                                <span class="menu-heading uppercase text-2sm font-semibold text-gray-500 pl-[10px] pr-[10px]">
                                    Apps
                                </span>
                            </div>
                            @php $headingMain = 1; @endphp
                        @elseif($section == 'master' && $headingMaster == 0)
                            <div class="menu-item pt-2.25 pb-px">
                                <span class="menu-heading uppercase text-2sm font-semibold text-gray-500 pl-[10px] pr-[10px]">
                                    Master Data
                                </span>
                            </div>
                            @php $headingMaster = 1; @endphp
                        @elseif($section == 'system' && $headingSystem == 0)
                            <div class="menu-item pt-2.25 pb-px">
                                <span class="menu-heading uppercase text-2sm font-semibold text-gray-500 pl-[10px] pr-[10px]">
                                    Systems
                                </span>
                            </div>
                            @php $headingSystem = 1; @endphp
                        @endif

                        @foreach($menus->$section as $menu)
                            @if(auth()->user()->hasRole($menu->roles))
                                @if(isset($menu->sub))
                                    <div class="menu-item {{ request()->routeIs($menu->path) || request()->routeIs($menu->path.'.*') ? 'show' : '' }}" data-menu-item-toggle="accordion" data-menu-item-trigger="click">
                                        <div
                                            class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px]"
                                            tabindex="0">
                                            <span class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px]">
                                                <i class="{{ $menu->icon ?? 'ki-filled ki-element-11 text-lg' }}"></i>
                                            </span>
                                            <span class="menu-title text-sm font-semibold text-gray-700 menu-item-active:text-primary menu-link-hover:!text-primary">
                                                {{ $menu->title }}
                                            </span>
                                            <span class="menu-arrow text-gray-400 w-[20px] shrink-0 justify-end ml-1 mr-[-10px]">
                                                <i class="ki-filled ki-plus text-2xs menu-item-show:hidden">
                                                </i>
                                                <i class="ki-filled ki-minus text-2xs hidden menu-item-show:inline-flex">
                                                </i>
                                            </span>
                                        </div>
                                        @if(is_array($menu->sub))
                                            <div class="menu-accordion gap-0.5 pl-[10px] relative before:absolute before:left-[20px] before:top-0 before:bottom-0 before:border-l before:border-gray-200">
                                                @foreach($menu->sub as $sub)
                                                    @if(auth()->user()->hasRole($sub->roles))
                                                        <div class="menu-item {{ request()->routeIs($sub->path.'.*') && in_array(request()->route()->getName(), [$sub->path.'.index', $sub->path.'.create', $sub->path.'.edit']) ? 'active' : '' }}">
                                                            <a class="menu-link gap-[14px] pl-[10px] pr-[10px] py-[8px] border border-transparent items-center grow menu-item-active:bg-secondary-active dark:menu-item-active:bg-coal-300 dark:menu-item-active:border-gray-100 menu-item-active:rounded-lg hover:bg-secondary-active dark:hover:bg-coal-300 dark:hover:border-gray-100 hover:rounded-lg"
                                                               href="{{ $sub->path ? route($sub->path.'.index') : '' }}" tabindex="0">
                                                                <span
                                                                    class="menu-bullet flex w-[6px] relative before:absolute before:top-0 before:size-[6px] before:rounded-full before:-translate-x-1/2 before:-translate-y-1/2 menu-item-active:before:bg-primary menu-item-hover:before:bg-primary">
                                                                </span>
                                                                <span
                                                                    class="menu-title text-2sm font-medium text-gray-700 menu-item-active:text-primary menu-item-active:font-semibold menu-link-hover:!text-primary">
                                                                    {{ $sub->title  }}
                                                                </span>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="menu-item {{ request()->routeIs($menu->path.'.*') ? 'active' : '' }}">
                                        <a class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px]" href="{{ $menu->path ? route($menu->path.'.index') : '' }}">
                                            <span class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px] menu-item-active:text-primary menu-link-hover:!text-primary">
                                                <i class="{{ $menu->icon ?? 'ki-filled ki-element-11 text-lg' }}"></i>
                                            </span>
                                            <span class="menu-title text-sm font-semibold text-gray-700 menu-item-active:text-primary menu-link-hover:!text-primary">
                                                {{ $menu->title }}
                                            </span>
                                        </a>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
