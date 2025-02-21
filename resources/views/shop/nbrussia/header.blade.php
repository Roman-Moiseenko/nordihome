<header>
    <div class="header-mobile">
        <div>
            <a class="navbar-brand" href="{{ url('/') }}">
                <svg width="59px" height="28px" viewBox="0 0 59 28"><title>logo</title><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-248.000000, -62.000000)" fill="#CF0A2C"><g transform="translate(0.000000, 36.000000)"><g transform="translate(248.000000, 26.000000)"><path d="M29.9145594,9.88941849 L32.6463591,5.19161106 L41.9236592,4.60438513 L42.2482294,4.03050524 L33.6741649,3.44327931 L35.6892053,0.0133460439 L51.5931483,0.0133460439 C56.4887498,0.0133460439 60.099594,1.74833174 58.6931228,6.80648236 C58.2738862,8.35462345 56.2858934,12.2249762 50.4436286,13.8398475 C51.6878146,13.986654 54.7712321,15.3346044 54.2302817,18.8312679 C53.2836184,25.0371783 45.1558381,27.986654 40.6253781,27.986654 L21.8408741,28 L20.9077345,24.4099142 L30.7530326,23.729266 L31.0911266,23.1553861 L20.4073554,22.514776 L19.2578357,18.257388 L34.4450193,17.3765491 L34.7831134,16.8160153 L7.50568737,15.1344137 L8.81749219,12.8922784 L38.1911011,11.0104862 L38.5291952,10.4499523 L29.9145594,9.88941849 M42.2076582,10.8236416 L44.9124104,10.8102955 C46.7245943,10.7969495 48.6043971,9.9828408 49.2941089,8.34127741 C49.9297257,6.80648236 49.0777288,5.49857007 47.9417328,5.51191611 L45.3181232,5.51191611 L42.2076582,10.8236416 Z M39.0836694,16.1487131 L35.824443,21.7407054 L38.934908,21.7407054 C40.3819504,21.7407054 42.9649888,21.0333651 43.7628907,19.0047664 C44.5066975,17.1096282 43.1407977,16.1487131 42.1941344,16.1487131 L39.0836694,16.1487131 L39.0836694,16.1487131 Z M15.39004,24.7836034 L13.4967135,27.9733079 L0,28 L1.31180482,25.7578646 L15.39004,24.7836034 L15.39004,24.7836034 Z M16.3367033,0 L27.1962834,0.0133460439 L28.0212328,3.042898 L15.051946,2.24213537 L16.3367033,0 Z M28.6298021,5.44518589 L29.8198931,9.88941849 L11.2788167,8.68827455 L12.5770978,6.45948522 L28.6298021,5.44518589 Z M19.1902169,18.257388 L16.8370825,22.2878932 L3.75960556,21.5538608 L5.07141038,19.3117255 L19.1902169,18.257388 L19.1902169,18.257388 Z"></path></g></g></g></g></svg>
            </a>
        </div>
        <div>
            <i class="fa-light fa-location-dot"></i>&nbsp;Россия
        </div>
    </div>
    <div class="menu-top py-0 hide-mobile">
        <div class="menu-container container-xl">
            <div class="d-flex justify-content-between">
            <div class="d-flex  my-auto"><i class="fa-light fa-location-dot my-auto"></i>&nbsp;Россия</div>
            <div class="d-flex ">
                <div>
                    @foreach(\App\Modules\NBRussia\Helper\MenuHelper::getMenuPages() as $item)
                        <a href="{{ $item['route'] }}"
                           class="fs-6 ms-1 link-dark link-underline-opacity-0 link-underline-opacity-0-hover fw-bolder link-opacity-75-hover">
                            {{ $item['name'] }}
                        </a>
                    @endforeach
                </div>
                <div style="margin: auto 0;">
                    <a href="tel:89812009869" style="color: #000;">8 (981) 200-98-69</a>
                </div>
                <div class="d-flex ms-2">
                    @foreach(\App\Modules\NBRussia\Helper\MenuHelper::getMenuContacts() as $item)
                        <div class="ms-2">
                            <a href="{{ $item['url'] }}" target="_blank" title="{{ $item['name'] }}">
                                <i class="{{ $item['icon'] }} fs-3" style="color: #333"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        </div>
    </div>

    <nav class="menu-bottom navbar navbar-expand-md navbar-light bg-white">
        <div class="menu-container container-xl">
            <div class="menu-bottom-catalog">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <svg width="59px" height="28px" viewBox="0 0 59 28"><title>logo</title><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-248.000000, -62.000000)" fill="#CF0A2C"><g transform="translate(0.000000, 36.000000)"><g transform="translate(248.000000, 26.000000)"><path d="M29.9145594,9.88941849 L32.6463591,5.19161106 L41.9236592,4.60438513 L42.2482294,4.03050524 L33.6741649,3.44327931 L35.6892053,0.0133460439 L51.5931483,0.0133460439 C56.4887498,0.0133460439 60.099594,1.74833174 58.6931228,6.80648236 C58.2738862,8.35462345 56.2858934,12.2249762 50.4436286,13.8398475 C51.6878146,13.986654 54.7712321,15.3346044 54.2302817,18.8312679 C53.2836184,25.0371783 45.1558381,27.986654 40.6253781,27.986654 L21.8408741,28 L20.9077345,24.4099142 L30.7530326,23.729266 L31.0911266,23.1553861 L20.4073554,22.514776 L19.2578357,18.257388 L34.4450193,17.3765491 L34.7831134,16.8160153 L7.50568737,15.1344137 L8.81749219,12.8922784 L38.1911011,11.0104862 L38.5291952,10.4499523 L29.9145594,9.88941849 M42.2076582,10.8236416 L44.9124104,10.8102955 C46.7245943,10.7969495 48.6043971,9.9828408 49.2941089,8.34127741 C49.9297257,6.80648236 49.0777288,5.49857007 47.9417328,5.51191611 L45.3181232,5.51191611 L42.2076582,10.8236416 Z M39.0836694,16.1487131 L35.824443,21.7407054 L38.934908,21.7407054 C40.3819504,21.7407054 42.9649888,21.0333651 43.7628907,19.0047664 C44.5066975,17.1096282 43.1407977,16.1487131 42.1941344,16.1487131 L39.0836694,16.1487131 L39.0836694,16.1487131 Z M15.39004,24.7836034 L13.4967135,27.9733079 L0,28 L1.31180482,25.7578646 L15.39004,24.7836034 L15.39004,24.7836034 Z M16.3367033,0 L27.1962834,0.0133460439 L28.0212328,3.042898 L15.051946,2.24213537 L16.3367033,0 Z M28.6298021,5.44518589 L29.8198931,9.88941849 L11.2788167,8.68827455 L12.5770978,6.45948522 L28.6298021,5.44518589 Z M19.1902169,18.257388 L16.8370825,22.2878932 L3.75960556,21.5538608 L5.07141038,19.3117255 L19.1902169,18.257388 L19.1902169,18.257388 Z"></path></g></g></g></g></svg>
                </a>
                <livewire:n-b-russia.header.category />
            </div>
            <!--div class="menu-bottom-search flex-grow-1">
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
            </div-->
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
                        <livewire:shop.header.wish :user="$user"/>
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
                        <livewire:shop.header.cart />
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
                <a href="{{ route('shop.home') }}" class="nav-link d-flex flex-column text-center">
                    <!--img src="/images/ikea.svg" style="height: 40px;"-->
                    <i class="fa-light fa-hundred-points"></i>
                    <!--i class="fa-light fa-lightbulb fs-3"></i-->
                    <!--i class="fa-sharp fa-light fa-box-open fs-3"></i-->
                    <span class="fs-8">Акции</span>
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


