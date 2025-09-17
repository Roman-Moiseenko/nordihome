<header>

    <div class="header-mobile">
        <div>
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ $config["logo-nav"] }}" alt="{{ $config["brand-alt"] }}" class="img-fluid img-logo">
            </a>
        </div>
        <div>
            <i class="fa-light fa-location-dot"></i>&nbsp;Россия
        </div>
    </div>
    <div class="menu-top container-xl mt-2 hide-mobile">
        <div class="d-flex justify-content-between">
            <div><i class="fa-light fa-location-dot"></i>&nbsp;Россия</div>
            <div class="d-flex ">
                <div>
                    @foreach(\App\Modules\Nordihome\Helper\MenuHelper::getMenuPages() as $item)
                        <a href="{{ $item['route'] }}"
                           class="fs-6 ms-1 link-dark link-underline-opacity-0 link-underline-opacity-0-hover fw-bolder link-opacity-75-hover">
                            {{ $item['name'] }}
                        </a>
                    @endforeach
                </div>
                <div class="d-flex ms-2">

                    @foreach(\App\Modules\Nordihome\Helper\MenuHelper::getMenuContacts() as $item)
                        <div class="ms-2">
                            <a href="{{ $item['url'] }}" target="_blank" title="{{ $item['name'] }}">
                                <i class="{{ $item['icon'] }} fs-3" style="color: {{ $item['color'] }}"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <nav class="menu-bottom navbar navbar-expand-md navbar-light bg-white">
        <div class="menu-container container-xl">
            <div class="menu-bottom-catalog d-flex">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="/images/nordihome/logo-nordi-home-2.svg" alt="Nordi Home" class="img-fluid img-logo">
                </a>
                <livewire:nordihome.header.category />

            </div>
            <div class="menu-bottom-search flex-grow-1">
                <div class="presearch" data-route="{{ route('shop.product.search') }}">
                    <div class="presearch-wrapper">
                        <input id="pre-search" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');">
                        <div class="presearch-suggest" style="display: none">
                        </div>
                        <div class="presearch-control fs-5 opacity-50">
                            <span id="presearch--icon-clear" class="presearch-icon clear" style="display:none;"><i
                                    class="fa-sharp fa-light fa-xmark"></i></span>
                            <span id="presearch--icon-search" class="presearch-icon search"><i
                                    class="fa-light fa-magnifying-glass"></i></span>
                        </div>
                    </div>
                    <div class="presearch-overlay" style="display: none"></div>
                </div>
            </div>

            <div class="menu-bottom-profile">
                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a id="login" class="nav-link d-flex flex-column text-center" href="#" data-bs-toggle="modal" data-bs-target="#login-popup">
                                    <i class="fa-light fa-user-vneck fs-4"></i>
                                    <span class="fs-7">Войти</span>
                                </a>
                            </li>

                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link d-flex flex-column text-center" href="{{ route('cabinet.view') }}">
                                <i class="fa-light fa-user-vneck fs-4"></i>
                                <span class="fs-7">Кабинет</span>
                            </a>
                        </li>
                    @endguest

                    <li class="nav-item">

                        <livewire:shop.header.wish />
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="{{ route('cabinet.order.index') }}"
                           @guest('user')
                           data-bs-toggle="modal" data-bs-target="#login-popup"
                            @endguest
                        >
                            <i class="fa-sharp fa-light fa-box-open fs-4"></i>
                            <span class="fs-7">Заказы</span>
                        </a>
                    </li>


                    <li class="nav-item">
                        <live wire:shop.header.cart />
                    </li>

                </ul>
            </div>
        </div>
    </nav>


    <nav class="menu-mobile">
        <ul class="menu-list">
            <li class="menu-item">
                <a href="{{ route('shop.home') }}" class="nav-link d-flex flex-column text-center">
                    <i class="fa-light fa-house fs-3"></i>
                    <span class="fs-8">Главная</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('shop.category.index') }}" class="nav-link d-flex flex-column text-center">
                    <i class="fa-light fa-folder-magnifying-glass fs-3"></i>
                    <span class="fs-8">Каталог</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('shop.cart.view') }}" class="nav-link d-flex flex-column text-center position-relative">
                    <span id="counter-cart" class="counter-cart counter" style="display: none;"></span>
                    <i class="fa-light fa-cart-shopping fs-3"></i>
                    <span class="fs-8">Корзина</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('shop.parser.view') }}" class="nav-link d-flex flex-column text-center">
                    <!--img src="/images/ikea.svg" style="height: 40px;"-->
                    <i class="fa-light fa-lightbulb fs-3"></i>
                    <!--i class="fa-sharp fa-light fa-box-open fs-3"></i-->
                    <span class="fs-8">ИКЕА</span>
                </a>
            </li>
            @guest
                @if (Route::has('login'))
                    <li class="menu-item">
                        <a id="login" class="nav-link d-flex flex-column text-center" href="#" data-bs-toggle="modal" data-bs-target="#login-popup">
                            <i class="fa-light fa-user-vneck fs-3"></i>
                            <span class="fs-8">Войти</span>
                        </a>
                    </li>

                @endif
            @else
                <li class="menu-item">
                    <a class="nav-link d-flex flex-column text-center" href="{{ route('cabinet.view') }}">
                        <i class="fa-light fa-user-vneck fs-3"></i>
                        <span class="fs-8">Кабинет</span>
                    </a>
                </li>
            @endguest
        </ul>
    </nav>

</header>


