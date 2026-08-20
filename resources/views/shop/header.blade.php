@php
    /** @var App\Modules\Shop\Application\DTOs\Menu\MenuData[] $menus */
/** @var  App\Modules\Shop\Application\DTOs\Menu\ContactData[] $contacts*/
@endphp

<header>
    <div class="header-mobile">
        <div class="menu-top container-xl mt-2">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <div>
                        @if(isset($menus['menu-header01']))
                            <ul id="menu-menyu-v-shapke" class="h-menu">
                                @foreach($menus['menu-header01']->items as $item)
                                    <li><a href="{{ $item->url }}">{{ $item->name }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            Меню не найдено
                        @endif
                    </div>
                    <div class="d-flex ms-2">

                        @foreach($contacts as $item)
                            <div class="ms-2">
                                <a href="{{ $item->url }}" target="_blank" title="{{ $item->name }}">
                                    @if(is_null($item->svg))
                                        <i class="{{ $item->icon }} fs-3" style="color: {{ $item->color }}"></i>
                                    @else
                                        {!! $item->svg !!}
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="menu-top container-xl mt-2 hide-mobile">
        <div class="d-flex justify-content-between">
            <div class="d-flex">
                <div>
                    @if(isset($menus['menu-header01']))
                        <ul id="menu-menyu-v-shapke" class="h-menu">
                            @foreach($menus['menu-header01']->items as $item)
                                <li><a href="{{ $item->url }}">{{ $item->name }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        Меню не найдено
                    @endif
                </div>
{{--<div class="d-flex ms-2">

    @foreach($contacts as $item)
        <div class="ms-2">
            <a href="{{ $item->url }}" target="_blank" title="{{ $item->name }}">
                @if(is_null($item->svg))
                    <i class="{{ $item->icon }} fs-3" style="color: {{ $item->color }}"></i>
                @else
                    {!! $item->svg !!}
                @endif
            </a>
        </div>
    @endforeach
         {!! $contacts['vk']->url !!}
</div>--}}
</div>
</div>
</div>

<nav class="menu-bottom navbar navbar-expand-md navbar-light bg-white">
<div class="menu-container container-xl">
<div class="menu-bottom-catalog d-flex">
<a class="navbar-brand" href="{{ url('/') }}">
    <img src="/uploads/gallery/7/nordi-home-rus.svg" alt="Nordi Home" class="img-fluid img-logo">
    <div class="h-city-text">склады находятся в г.Калининград</div>
</a>
<!--- <a href="{{ route('shop.category.index') }}">Категории</a>

<a href="{{ route('shop.room.index') }}">Комнаты</a>
 --->
<div class="header-menu-buttons d-flex">
    <!-- Главные кнопки открываются по клику -->
    <button class="nav-link header-menu-buttons-item m-r_5" type="button" data-target="catalogMenu">
        <div class="header-menu-buttons-item-burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div>Каталог</div>
    </button>
    <button class="nav-link header-menu-buttons-item" type="button" data-target="roomsMenu">
        <div class="header-menu-buttons-item-burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div>Комнаты</div>
    </button>
</div>
<div class="header-menu-mobile-btn" id="menuToggle">
    <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <div>Каталог</div>
</div>
@include('shop.widgets.header.category')

</div>
<div class="menu-bottom-search flex-grow-1">
@include('shop.widgets.header.search')
</div>

<div class="menu-bottom-profile">
<ul class="navbar-nav ms-auto">
    @client
    <li class="nav-item">
        <a class="nav-link d-flex flex-column text-center" href="{{ route('cabinet.view') }}">
            <i class="fa-light fa-user-vneck fs-4"></i>
            <span class="fs-7">Кабинет</span>
        </a>
    </li>
    @else
        <li class="nav-item">
            <a id="login" class="nav-link d-flex flex-column text-center" href="#"
               data-bs-toggle="modal" data-bs-target="#login-popup">
                <i class="fa-light fa-user-vneck fs-4"></i>
                <span class="fs-7">Войти</span>
            </a>
        </li>
        @endclient

        <li class="nav-item">
            <livewire:header.wish/>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex flex-column text-center" href="{{ route('cabinet.order.index') }}"
               @notclient
               data-bs-toggle="modal" data-bs-target="#login-popup"
                @endnotclient
            >
                <i class="fa-sharp fa-light fa-box-open fs-4"></i>
                <span class="fs-7">Заказы</span>
            </a>
        </li>

        <li class="nav-item">
            <livewire:header.cart/>
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
<a href="{{ route('shop.cart.view') }}"
   class="nav-link d-flex flex-column text-center position-relative">
    <span id="counter-cart" class="counter-cart counter" style="display: none;"></span>
    <i class="fa-light fa-cart-shopping fs-3"></i>
    <span class="fs-8">Корзина</span>
</a>
</li>

<li class="menu-item">
<a href="{{ route('shop.ikea.index') }}" class="nav-link d-flex flex-column text-center">
    <!--img src="/images/ikea.svg" style="height: 40px;"-->
    <i class="fa-light fa-lightbulb fs-3"></i>
    <!--i class="fa-sharp fa-light fa-box-open fs-3"></i-->
    <span class="fs-8">ИКЕА</span>
</a>
</li>


@client
<li class="menu-item">
<a class="nav-link d-flex flex-column text-center" href="{{ route('cabinet.view') }}">
    <i class="fa-light fa-user-vneck fs-3"></i>
    <span class="fs-8">Кабинет</span>
</a>
</li>


@else
<li class="menu-item">
    <a id="login" class="nav-link d-flex flex-column text-center" href="#" data-bs-toggle="modal"
       data-bs-target="#login-popup">
        <i class="fa-light fa-user-vneck fs-3"></i>
        <span class="fs-8">Войти</span>
    </a>
</li>
@endclient
</ul>
</nav>

</header>


