<!-- BEGIN: Mobile Menu -->

<div @class([
    'mobile-menu group w-full fixed bg-primary/90 z-[60] border-b border-white/[0.08] -mt-5 -mx-3 sm:-mx-8 mb-6 dark:bg-darkmode-800/90 md:hidden',
    "before:content-[''] before:w-full before:h-screen before:z-10 before:fixed before:inset-x-0 before:bg-black/90 before:transition-opacity before:duration-200 before:ease-in-out",
    'before:invisible before:opacity-0',
    '[&.mobile-menu--active]:before:visible [&.mobile-menu--active]:before:opacity-100',
])>
    <div class="flex h-[70px] items-center px-3 sm:px-8">
        <a class="mr-auto flex" href="">
            <img class="w-6" src="{{ Vite::asset('resources/images/logo.svg') }}" alt=""/>
        </a>
        <a class="mobile-menu-toggler" href="#">
            <i data-lucide="bar-chart-2" width="24" height="24" class="h-8 w-8 -rotate-90 transform text-white"></i>
        </a>
    </div>
    <div @class([
        'scrollable h-screen z-20 top-0 left-0 w-[270px] -ml-[100%] bg-primary transition-all duration-300 ease-in-out dark:bg-darkmode-800',
        '[&[data-simplebar]]:fixed [&_.simplebar-scrollbar]:before:bg-black/50',
        'group-[.mobile-menu--active]:ml-0',
    ])>
        <a
            href="#"
            @class([
                'fixed top-0 right-0 mt-4 mr-4 transition-opacity duration-200 ease-in-out',
                'invisible opacity-0',
                'group-[.mobile-menu--active]:visible group-[.mobile-menu--active]:opacity-100',
            ])
        >
            <i data-lucide="x-circle" width="24" height="24" class="mobile-menu-toggler h-8 w-8 -rotate-90 transform text-white"></i>
        </a>
        <ul class="py-2">
            <!-- BEGIN: First Child -->
            @foreach ($sideMenu as $menuKey => $menu)
                @if (strpos($menuKey, 'divider') != false)
                    <li class="menu__divider my-6"></li>
                @else
                    <li>
                        <a
                            class="{{ $firstLevelActiveIndex == $menuKey ? 'menu menu--active' : 'menu' }}"
                            href="{{ isset($menu['route_name']) ? route($menu['route_name']) : 'javascript:;' }}"
                        >
                            <div class="menu__icon">
                                <i data-lucide="{{ $menu['icon'] ?? '' }}" width="24" height="24"></i>
                            </div>
                            <div class="menu__title">
                                {{ $menu['title'] }}
                                @if (isset($menu['sub_menu']))
                                    <div
                                        class="menu__sub-icon {{ $firstLevelActiveIndex == $menuKey ? 'transform rotate-180' : '' }}">
                                        <i data-lucide="chevron-down" width="24" height="24"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                        @if (isset($menu['sub_menu']))
                            <ul class="{{ $firstLevelActiveIndex == $menuKey ? 'menu__sub-open' : '' }}">
                                @foreach ($menu['sub_menu'] as $subMenuKey => $subMenu)
                                    <li>
                                        <a class="{{ $secondLevelActiveIndex == $subMenuKey ? 'menu menu--active' : 'menu' }}"
                                            href="{{ isset($subMenu['route_name']) ? route($subMenu['route_name']) : 'javascript:;' }}">
                                            <div class="menu__icon">
                                                <i data-lucide="{{ $subMenu['icon'] ?? '' }}" width="24" height="24"></i>
                                            </div>
                                            <div class="menu__title">
                                                {{ $subMenu['title'] }}
                                                @if (isset($subMenu['sub_menu']))
                                                    <div
                                                        class="menu__sub-icon {{ $secondLevelActiveIndex == $subMenuKey ? 'transform rotate-180' : '' }}">
                                                        <i data-lucide="chevron-down" width="24" height="24"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </a>
                                        @if (isset($subMenu['sub_menu']))
                                            <ul
                                                class="{{ $secondLevelActiveIndex == $subMenuKey ? 'menu__sub-open' : '' }}">
                                                @foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu)
                                                    <li>
                                                        <a class="{{ $thirdLevelActiveIndex == $lastSubMenuKey ? 'menu menu--active' : 'menu' }}"
                                                            href="{{ isset($lastSubMenu['route_name']) ? route($lastSubMenu['route_name']) : 'javascript:;' }}">
                                                            <div class="menu__icon">
                                                                <i data-lucide="{{ $lastSubMenu['icon'] ?? '' }}" width="24" height="24"></i>
                                                            </div>
                                                            <div class="menu__title">{{ $lastSubMenu['title'] }}</div>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
            @endforeach
            <!-- END: First Child -->
        </ul>
    </div>
</div>
<!-- END: Mobile Menu -->


