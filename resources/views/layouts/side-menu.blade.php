@extends('layouts.admin')

@section('head')
    @yield('subhead')
@endsection

@section('content')
    <div class="py-5 md:py-0">
        @include('admin.components.mobile-menu')
        @include('admin.components.top-bar', ['current_user' => $admin] )
        <div class="flex overflow-hidden">
            <!-- BEGIN: Side Menu -->
            <nav class="side-nav z-50 -mt-4 hidden w-[105px] overflow-x-hidden px-5 pb-16 pt-32 md:block xl:w-[260px]">
                <ul>
                    @foreach ($sideMenu as $menuKey => $menu)
                        @if ($menu == 'divider')
                            <li class="side-nav__divider my-6"></li>
                        @else
                            @canany($menu['can'] ?? '')
                                <li>
                                    <a href="{{ isset($menu['route_name']) ? route($menu['route_name']) : 'javascript:;' }}"
                                       class="{{ $firstLevelActiveIndex == $menuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                        <div class="side-menu__icon">
                                            <x-base.lucide icon="{{ $menu['icon'] }}" />
                                        </div>
                                        <div class="side-menu__title">
                                            {{ $menu['title'] }}
                                            @if (isset($menu['sub_menu']))
                                                <div
                                                    class="side-menu__sub-icon {{ $firstLevelActiveIndex == $menuKey ? 'transform rotate-180' : '' }}">
                                                    <x-base.lucide icon="chevron-down" />
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    @if (isset($menu['sub_menu']))
                                        <ul class="{{ $firstLevelActiveIndex == $menuKey ? 'side-menu__sub-open' : '' }}">
                                            @foreach ($menu['sub_menu'] as $subMenuKey => $subMenu)
                                                @canany($subMenu['can'] ?? '')
                                                <li>
                                                    <a href="{{ isset($subMenu['route_name']) ? route($subMenu['route_name']) : 'javascript:;' }}"
                                                       class="{{ $secondLevelActiveIndex == $subMenuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                        <div class="side-menu__icon">
                                                            <x-base.lucide icon="{{ $subMenu['icon'] }}" />
                                                        </div>
                                                        <div class="side-menu__title">
                                                            {{ $subMenu['title'] }}
                                                            @if (isset($subMenu['sub_menu']))
                                                                <div
                                                                    class="side-menu__sub-icon {{ $secondLevelActiveIndex == $subMenuKey ? 'transform rotate-180' : '' }}">
                                                                    <x-base.lucide icon="chevron-down" />
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </a>
                                                    @if (isset($subMenu['sub_menu']))
                                                        <ul
                                                            class="{{ $secondLevelActiveIndex == $subMenuKey ? 'side-menu__sub-open' : '' }}">
                                                            @foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu)
                                                                @canany($lastSubMenu['can'] ?? '')
                                                                <li>
                                                                    <a href="{{ isset($lastSubMenu['route_name']) ? route($lastSubMenu['route_name']) : 'javascript:;' }}"
                                                                       class="{{ $thirdLevelActiveIndex == $lastSubMenuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                                        <div class="side-menu__icon">
                                                                            <x-base.lucide icon="{{ $lastSubMenu['icon'] }}" />
                                                                        </div>
                                                                        <div class="side-menu__title">
                                                                            {{ $lastSubMenu['title'] }}
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                                @endcanany
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                                @endcanany
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endcanany
                        @endif
                    @endforeach
                </ul>
            </nav>
            <!-- END: Side Menu -->
            <!-- BEGIN: Content -->
            <div class="content">
                @include('flash::message')
                @yield('subcontent')
            </div>
            <!-- END: Content -->
        </div>
    </div>
@endsection
@once
    @push('scripts')
        @vite('resources/js/vendor/tippy/index.js')
    @endpush
@endonce

@once
    @push('scripts')
        @vite('resources/js/layouts/side-menu/index.js')
    @endpush
@endonce
