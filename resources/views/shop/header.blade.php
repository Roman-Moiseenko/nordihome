<header>
    <div class="menu-top container-xl mt-2">
        <div class="d-flex justify-content-between">
            <div><i class="fa-light fa-location-dot"></i>&nbsp;Калининград</div>
            <div class="d-flex ">
                <div>
                    @foreach(\App\Modules\Shop\MenuHelper::getMenuPages() as $item)
                        <a href="{{ $item['route'] }}"
                           class="fs-6 ms-1 link-dark link-underline-opacity-0 link-underline-opacity-0-hover fw-bolder link-opacity-75-hover">
                            {{ $item['name'] }}
                        </a>
                    @endforeach
                </div>
                <div class="d-flex ms-2">

                    @foreach(\App\Modules\Shop\MenuHelper::getMenuContacts() as $item)
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
    <nav class="menu-bottom navbar navbar-expand-md navbar-light bg-white shadow-sm d-block">

        <div class="d-flex flex-row text-center align-items-center container-xl">
            <div class="menu-bottom-catalog">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ $config['logo-nav'] }}" alt="{{ $config['brand-alt'] }}" class="img-fluid img-logo">
                </a>
                @include('shop.widgets.header.category',['categories' => $categories])
            </div>
            <div class="menu-bottom-search flex-grow-1 mx-3">
                <div class="presearch" data-route="{{ route('shop.product.search') }}">
                    <div class="presearch-wrapper">
                        <input id="pre-search" class="presearch-input">
                        <div class="presearch-suggest" style="display: none">
                        </div>
                        <div class="presearch-control fs-5 opacity-50">
                            <span id="presearch--icon-clear" class="presearch--icon-clear" style="display:none;"><i
                                    class="fa-sharp fa-light fa-xmark"></i></span>
                            <span id="presearch--icon-search" class="presearch--icon-search"><i
                                    class="fa-light fa-magnifying-glass"></i></span>
                        </div>
                    </div>
                    <div class="presearch-overlay" style="display: none"></div>
                </div>
            </div>
            <div class="menu-bottom-profile d-flex">
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
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
                            <a class="nav-link d-flex flex-column text-center" href="{{ route('login') }}">
                                <i class="fa-light fa-user-vneck fs-4"></i>
                                <span class="fs-7">Кабинет</span>
                            </a>
                        </li>
                    @endguest
                    <li class="nav-item">
                        @include('shop.widgets.header.wish')
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="{{ route('shop.order.index') }}"
                           @guest('user')
                           data-bs-toggle="modal" data-bs-target="#login-popup"
                            @endguest
                        >
                            <i class="fa-sharp fa-light fa-box-open fs-4"></i>
                            <span class="fs-7">Заказы</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        @include('shop.widgets.header.cart')
                    </li>


                </ul>
            </div>
        </div>

    </nav>
</header>
@guest
    @include('user.auth.login-popup')
@endguest
