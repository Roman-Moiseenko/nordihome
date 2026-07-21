@php
    //Глобальные данные
    /** @var \App\Modules\Shop\Application\DTOs\CategoryTreeClientData[] $categoryTree */
    /** @var \App\Modules\Shop\Application\DTOs\RoomTreeClientData[] $roomTree */
@endphp

    <!-- 1. КАТАЛОГ -->
    <div class="mega-menu-panel" id="catalogMenu">
        <div class="container p-0">
            <div class="row g-0">
                <div class="col-3 border-end py-3">
                    <div class="nav flex-column nav-pills nav-pills-custom" role="tablist">
                        <!-- Пункты переключаются при наведении -->
                        @include('shop.widgets.header._left-menu', ['categories' => $categoryTree, 'entity' => 'category'])
                    </div>
                </div>
                <div class="col-9 p-4">
                    <div class="tab-content">
                        @include('shop.widgets.header._right-menu', ['categories' => $categoryTree, 'entity' => 'category'])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. КОМНАТЫ -->
    <div class="mega-menu-panel" id="roomsMenu">
        <div class="container p-0">
            <div class="row g-0">
                <div class="col-3 border-end py-3">
                    <div class="nav flex-column nav-pills nav-pills-custom" role="tablist">
                        <!-- Пункты переключаются при наведении -->
                        @include('shop.widgets.header._left-menu', ['categories' => $roomTree, 'entity' => 'room'])
                    </div>
                </div>
                <div class="col-9 p-4">
                    <div class="tab-content">
                        @include('shop.widgets.header._right-menu', ['categories' => $roomTree, 'entity' => 'room'])
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- МОБ ВЕРСИЯ -->
<nav class="header-menu-mobile" id="mobileMenu">
    <div class="m-b_20 header-menu-mobile-buttons">
        <a href="#mobileMenuCatalog">Каталог</a>
        <a href="#mobileMenuRooms">По комнатам</a>
    </div>
    <div class="header-menu-mobile-body">
        <div id="mobileMenuCatalog"></div>
        <div class="f-w_600 m-b_20">Каталог</div>
        <ul class="">
            @include('shop.widgets.header._mobile-menu', ['categories' => $categoryTree, 'entity' => 'category'])
        </ul>
        <div id="mobileMenuRooms"></div>
        <div class="f-w_600 m-b_20 m-t_20">По комнатам</div>
        <ul class="">
            @include('shop.widgets.header._mobile-menu', ['categories' => $roomTree, 'entity' => 'room'])
        </ul>
    </div>
</nav>
