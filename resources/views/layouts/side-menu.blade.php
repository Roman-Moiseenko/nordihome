@extends('layouts.admin')

@section('head')
    @yield('subhead')
@endsection

@section('content')
    <div class="py-5 md:py-0">
        @include('admin.components.mobile-menu')
        @include('admin.components.top-bar')
        <div class="flex overflow-hidden">
            <!-- BEGIN: Side Menu -->
            <nav class="side-nav z-50 -mt-4 hidden w-[105px] overflow-x-hidden px-5 pb-16 pt-32 md:block xl:w-[260px]">
                <ul>
                    <!-- BEGIN: First Child -->
                    @foreach ($sideMenu as $menuKey => $menu)
                        @if ($menu == 'divider')
                            <li class="side-nav__divider my-6"></li>
                        @else
                            <li>
                                <a href="{{ isset($menu['route_name']) ? route($menu['route_name'], $menu['params']) : 'javascript:;' }}"
                                   class="{{ $firstLevelActiveIndex == $menuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                    <div class="side-menu__icon">
                                        <i data-lucide="{{ uncamelize($menu['icon'], '-') }}" width="24" height="24"></i>
                                    </div>
                                    <div class="side-menu__title">
                                        {{ $menu['title'] }}
                                        @if (isset($menu['sub_menu']))
                                            <div
                                                class="side-menu__sub-icon {{ $firstLevelActiveIndex == $menuKey ? 'transform rotate-180' : '' }}">
                                                <i data-lucide="chevron-down" width="24" height="24"></i>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                @if (isset($menu['sub_menu']))
                                    <ul class="{{ $firstLevelActiveIndex == $menuKey ? 'side-menu__sub-open' : '' }}">
                                        @foreach ($menu['sub_menu'] as $subMenuKey => $subMenu)
                                            <li>
                                                <a href="{{ isset($subMenu['route_name']) ? route($subMenu['route_name'], $subMenu['params']) : 'javascript:;' }}"
                                                   class="{{ $secondLevelActiveIndex == $subMenuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                    <div class="side-menu__icon">
                                                        <i data-lucide="{{ uncamelize($subMenu['icon'], '-') }}" width="24" height="24"></i>
                                                    </div>
                                                    <div class="side-menu__title">
                                                        {{ $subMenu['title'] }}
                                                        @if (isset($subMenu['sub_menu']))
                                                            <div
                                                                class="side-menu__sub-icon {{ $secondLevelActiveIndex == $subMenuKey ? 'transform rotate-180' : '' }}">
                                                                <i data-lucide="chevron-down" width="24" height="24"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </a>
                                                @if (isset($subMenu['sub_menu']))
                                                    <ul
                                                        class="{{ $secondLevelActiveIndex == $subMenuKey ? 'side-menu__sub-open' : '' }}">
                                                        @foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu)
                                                            <li>
                                                                <a href="{{ isset($lastSubMenu['route_name']) ? route($lastSubMenu['route_name'], $lastSubMenu['params']) : 'javascript:;' }}"
                                                                    class="{{ $thirdLevelActiveIndex == $lastSubMenuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                                    <div class="side-menu__icon">
                                                                        <i data-lucide="{{ uncamelize($lastSubMenu['icon'], '-') }}" width="24" height="24"></i>
                                                                    </div>
                                                                    <div class="side-menu__title">
                                                                        {{ $lastSubMenu['title'] }}
                                                                    </div>
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
            </nav>
            <!-- END: Side Menu -->
            <!-- BEGIN: Content -->
            <div class="content">
                @yield('subcontent')
            </div>
            <!-- END: Content -->
        </div>
    </div>

@endsection
