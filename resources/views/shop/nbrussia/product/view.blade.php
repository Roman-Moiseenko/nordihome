@extends('shop.nbrussia.layouts.main')

@section('body', 'product')
@section('main', 'container-xl product-page')
@section('title', empty($title) ? ($product['name'] . ' купить по цене ' . $product['price'] . '₽ ☛ Доставка по всей России ★★★ Интернет-магазин  ★★★ Оригинал Нью Баланс') : $title)
@section('description', empty($description) ? ('Оригинальный ' . $product['name'] . ' из Европы. Бесплатная доставка по всей России. Только брендовая одежда и обувь. Гарантия качества. Маркировка Честный знак') : $description)
@section('content')

    <div class="product-view">
        <div class="pre-block-gallery hide-mobile">
            <div class="block-gallery">
                <div>
                    <div class="gallery hide-mobile">
                        @foreach($product['gallery'] as $image)
                            <span class="item">
                                <span class="pre-item">
                                    <img
                                        src="data:image/svg+xml,%3csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20version=%271.1%27%20width=%27500%27%20height=%27500%27/%3e"
                                        height="500" width="500" alt="Блок задающий высоту ячейки"/>
                                </span>
                                <img src="{{ $image['src'] }}" alt="{{ empty($image['alt']) ? $product['name'] : $image['alt'] }}"
                                     decoding="async" />
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
        <div class="show-mobile">
            <div id="product-slider" class="owl-carousel owl-theme">
                @foreach($product['gallery'] as $image)
                    <div>
                        <img src="{{ $image['src'] }}" alt="{{ empty($image['alt']) ? $product['name'] : $image['alt'] }}"
                             decoding="async" />
                    </div>
                @endforeach
            </div>
        </div>
        <div class="pre-block-specification">
            <div class="specification">
                <h1>{{ $product['name'] }}</h1>
                <div class="category-name">{{ $product['category']['name'] }}</div>
                <div class="rating">
                    @include('shop.nbrussia.widgets.stars', ['rating' => $product['rating'], 'show_number' => true])
                    <a href="#reviews" title="Отзывы реальных покупателей"
                       aria-label="Отзывы реальных покупателей">({{ $product['count_reviews'] }})</a>
                </div>
                <div class="price">
                    <div class="current">{{ price($product['price']) }}</div>
                    @if ($product['price_previous'] > $product['price'])
                        <div class="previous">Базовая цена {{ price($product['price_previous']) }}</div>
                    @endif
                </div>
                <div class="equivalent">
                    @if (!empty($product['equivalents']))
                        @foreach($product['equivalents'] as $equivalent)
                            <div class="item">
                                <a href="{{ route('shop.product.view', $equivalent['slug']) }}"
                                   title="{{ $equivalent['name'] }}">
                                    <img src="{{ $equivalent['src'] }}" alt="{{ $equivalent['name'] }}"/>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="color">
                    <strong>Цвет</strong>
                    @foreach($productAttributes['Основные характеристики'] as $attr)
                        @if($attr['name'] == 'Цвет')
                            {{ $attr['value'] }}
                        @endif
                    @endforeach
                </div>
                <div class="available">
                    @if ($product['quantity'] > 0)
                        <span style="color: var(--bs-secondary-700);">В наличии</span>
                    @else
                        <a href="{{ route('shop.page.view', ['slug' => 'delivery']) }}">Доставка из Польши</a>
                    @endif
                </div>
                <div class="sizes">
                    <div class="caption">
                        <span>Доступные размеры</span>
                        <a data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"
                           aria-controls="offcanvasExample">
                            <i class="fa-light fa-ruler"></i> Таблица размеров
                        </a>
                    </div>
                    <div class="modification">
                        @foreach($product['modification'] as $attribute)
                            @if(isset($attribute['products']))
                                @foreach($attribute['products'] as $value => $_product_mod)
                                    @if($product['is_sale'])
                                    <span class="size {{ $product['id'] === $_product_mod[0]['id'] ? 'active' : '' }}"
                                          data-id="{{ $_product_mod[0]['id'] }}"
                                          title="{{ $_product_mod[0]['name'] }}"> {{ $value }}</span>
                                    @else
                                        <a class="size"
                                              href="{{ route('shop.product.view', $_product_mod[0]['id']) }}"
                                              title="{{ $_product_mod[0]['name'] }}"> {{ $value }}</a>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>


                <div class="d-flex mt-3">
                    @if($product['is_sale'])
                        <button id="to-cart" class="to-cart btn-nb" data-product="{{ $product['id'] }}" disabled
                                style="width: 100%;">В Корзину
                        </button>
                    @else
                        <button type="button" class="btn btn-secondary" disabled
                                style="width: 100%; padding: 1.2rem 2.4rem;"
                        >Нет в наличии</button>
                    @endif
                </div>
                <div class="mt-3 fs-6 fw-bold"><span style="color: var(--bs-secondary-700)"><a href="/page/delivery" title="Условия доставки">Бесплатная*</a> </span> доставка по России</div>
                <!--div class="fs-7 fw-bold">до 7 дней бесплатного возврата*</div-->
                <div class="mt-3">
                    <img src="{{ url('/images/true-sign-text-100.jpg') }}" alt="Маркировка Честный знак" style="height: 60px;"/>
                </div>
                <div class="description">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header py-3" id="headingOne">
                                <button class="accordion-button"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                        aria-expanded="true" aria-controls="collapseOne">
                                    Описание товара
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                 data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="fs-5 fw-bold">{{ $product['name'] }}</div>
                                    {!! $product['description'] !!}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header py-3" id="headingTwo">
                                <button class="accordion-button collapsed"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Детали товара
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                 data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul>
                                        @foreach($productAttributes['Основные характеристики'] as $attr)
                                            <li>
                                                <strong>{{ $attr['name'] }}</strong>: {{ $attr['value'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="mt-3">
                    <a href="{{ route('shop.category.view', $product['category']['slug']) }}">Все товары
                        из {{ $product['category']['name'] }}</a>
                </div>
                <div class="mt-3">
                    <button class="to-wish ps-0" data-bs-toggle="modal" data-bs-target="#login-popup"
                            type="button"
                            onclick="event.preventDefault();">
                        <i class="fa-light fa-heart" type="button" title="В Избранное"></i> Добавить в избранное
                    </button>
                </div>

            </div>
        </div>

        <!--div class="product-video">
            Видео товара
        </div>

        <div class="product-info-block">
            Инфо блоки
        </div>

        <div class="recently">
            Вы смотрели
        </div>

        <div id="reviews" class="product-review">
            Отзывы и рейтинг
        </div-->


        <div class="offcanvas offcanvas-end overflow-auto" tabindex="-1" id="offcanvasExample"
             aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Таблица размеров</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
            </div>
            <div class="offcanvas-body">
                <livewire:n-b-russia.table-sizes :category_id="$product['category']['id']"/>

            </div>
        </div>

    {!! $schema !!}
@endsection
