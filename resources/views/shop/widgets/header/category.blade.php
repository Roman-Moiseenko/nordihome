@php
    /** @var \App\Modules\Shop\Application\DTOs\CategoryTreeClientData[] $categoryTree */
    /** @var \App\Modules\Shop\Application\DTOs\RoomTreeClientData[] $roomTree */
@endphp

<!-- <div class="btn-group" role="group">
    <button type="button" class="btn btn-dark fs-5 ls-1 dropdown-toggle lh-sm" data-bs-toggle="dropdown"
            aria-expanded="false">Каталог&nbsp;
    </button>
    <div class="dropdown-menu">
        <div class="catalog">
            <div class="catalog-rootmenu">
                @foreach($categoryTree as $category)
                    <li>
                        <a class="dropdown-item" href="{{ route('shop.category.view', $category->slug) }}" data-id="{{ $category->id }}">

                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </div>
            <div class="catalog-submenu">
                <div id="catalog-submenu" class="catalog-submenu-scroll"></div>
            </div>
        </div>
    </div>
</div> -->

    <!-- 1. КАТАЛОГ -->
    <div class="mega-menu-panel" id="catalogMenu">
        <div class="container p-0">
            <div class="row g-0">
                <div class="col-3 border-end py-3">
                    <div class="nav flex-column nav-pills nav-pills-custom" role="tablist">
                        <!-- Пункты переключаются при наведении -->
                        <a href="/" class="nav-link active" data-pane="catalogMenu01"  role="tab">Диваны и кресла</a>
                        <a href="/" class="nav-link" data-pane="catalogMenu02"  role="tab">Кровати и матрасы</a>
                    </div>
                </div>
                <div class="col-9 p-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="catalogMenu01" role="tabpanel">
                            <div class="f-w_600 f-z_21 m-b_30">Диваны и кресла</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Диваны</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Прямые диваны</a></li>
                                            <li><a href="#">Угловые диваны</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Кресла</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Кресла 1</a></li>
                                            <li><a href="#">Кресла 2</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Диваны</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Прямые диваны</a></li>
                                            <li><a href="#">Угловые диваны</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Кресла</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Кресла 1</a></li>
                                            <li><a href="#">Кресла 2</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Диваны</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Прямые диваны</a></li>
                                            <li><a href="#">Угловые диваны</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Кресла</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Кресла 1</a></li>
                                            <li><a href="#">Кресла 2</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="catalogMenu02" role="tabpanel">
                            <div class="f-w_600 f-z_21 m-b_30">Кровати и матрасы</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Кровати</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Двуспальные кровати</a></li>
                                            <li><a href="#">Двуспальные кровати </a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Матрасы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Двуспальные кровати</a></li>
                                            <li><a href="#">Двуспальные кровати 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Кровати</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Двуспальные кровати</a></li>
                                            <li><a href="#">Двуспальные кровати </a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Матрасы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Двуспальные кровати</a></li>
                                            <li><a href="#">Двуспальные кровати 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Кровати</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Двуспальные кровати</a></li>
                                            <li><a href="#">Двуспальные кровати </a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Матрасы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Двуспальные кровати</a></li>
                                            <li><a href="#">Двуспальные кровати 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        <a href="/" class="nav-link active" data-pane="roomsMenu01"  role="tab">Гостиная</a>
                        <a href="/" class="nav-link" data-pane="roomsMenu02"  role="tab">Ванная</a>
                    </div>
                </div>
                <div class="col-9 p-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="roomsMenu01" role="tabpanel">
                            <div class="f-w_600 f-z_21 m-b_30">Гостиная</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Тумбы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 2</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Шкафы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Тумбы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 2</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Шкафы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Тумбы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 2</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Шкафы</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="roomsMenu02" role="tabpanel">
                            <div class="f-w_600 f-z_21 m-b_30">Ванная комната</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Тумбы для ванной</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 2</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Шкафы для ванной</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Тумбы для ванной</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 2</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Шкафы для ванной</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="submenu-links">
                                        <div class="f-w_600 m-b_20">Тумбы для ванной</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 2</a></li>
                                        </ul>
                                        <div class="f-w_600 m-b_20">Шкафы для ванной</div>
                                        <ul class="m-b_20">
                                            <li><a href="#">Подменю 1</a></li>
                                            <li><a href="#">Подменю 1</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
        </ul>
        <div id="mobileMenuRooms"></div>
        <div class="f-w_600 m-b_20 m-t_20">По комнатам</div>
        <ul class="">
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
            <li>
                <a href="/">
                    <img src="" alt=""> Картинка категории
                    <div>Название категории</div>
                </a>
            </li>
        </ul>
    </div>
</nav>
