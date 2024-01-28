@extends('layouts.shop')

@section('body')
    page calculate
@endsection

@section('main')
    container-xl
@endsection

@section('content')
    <h1>Заказ товаров с каталога IKEA.PL</h1>
    <section class="parser" id="parser-container">
    <div class="left-side" id="left-side">
        <div id="parser-search">
            <div class="parser-card-search">
                <div class="parser-card-search--header">
                    <p>Рассчитайте стоимость любого товара из каталога Икеа самостоятельно и Вы сразу узнаете стоимость заказа.</p>
                    <p><b>Для точного расчёта данный инструмент использовать без VPN</b></p>
                    <h3 id="parser-condition" class="_name_">Найти товар</h3>
                </div>

                <div class="parser-card-search--find">
                    <div id="parser-condition-text" class="parser-card-search--text">
                        Скопируйте и вставьте в поле номер артикула товара или ссылку с сайта <a href="https://IKEA.PL" target="_blank">IKEA.PL</a>
                    </div>
                    <form method="post" action="{{ route('shop.parser.search') }}">
                        @csrf
                    <div class="parser-card-search--form">

                        <input id="search-parser-field" type="text" name="search" class="form-control"/>
                        <button id="search-parser-button">Искать</button>

                    </div>
                </form>
                </div>
            </div>
        </div>
        <div id="parser-list">
            <div class="parsing-title-products">
                <div>
                    <h3>Товары в корзине:</h3>
                </div>
                <div class="parsing-title-products--button">
                    <a id="clear-button" class="btn btn-dark"
                       onclick="event.preventDefault(); document.getElementById('form-clear-parser').submit();">Очистить корзину</a>
                    <form id="form-clear-parser" method="post" action="{{ route('shop.parser.clear') }}">
                        @csrf
                    </form>
                </div>
            </div>
            @foreach($cart->items as $i => $item)
                <div class="parser-list-item" style="">
                    <div class="parser-item-img">
                        <img src="{{ $item->product->photo->getThumbUrl('thumb') }}" alt="{{ $item->product->name }}">
                    </div>
                    <div class="parser-item-data">
                        <h4>{{ $item->product->name }}</h4>
                        <div class="description-product">{{ $item->product->short }}</div>
                        <div><span>Артикул: </span><span class="code-selected">{{ $item->product->code }}</span></div>
                        <div><span>Вес: </span><strong>{{ $item->product->dimensions->weight }} кг</strong></div>
                        <div><span>Кол-во пачек: </span><strong>{{ $item->parser->packs }} шт.</strong></div>
                        <div><span>Наличие в ИКЕА: </span></div>
                        <div class="parser-item-quantity">{!! $item->storages !!}</div>
                        <div class="parser-list-item--bottom">
                            <div class="parser-list-item--cost">{{ price($item->cost * $item->quantity) }}</div>
                            <div class="parser-list-item--form">
                                <button id="delete-button"
                                        onclick="event.preventDefault(); document.getElementById('form-remove-{{$i}}').submit();">
                                    <i class="fa-light fa-trash"></i>
                                </button>
                                <form id="form-remove-{{$i}}" method="post" action="{{ route('shop.parser.remove', $item->product) }}">
                                    @csrf
                                </form>
                                <button class="decrease-button" data-code="{{ $item->product->id }}"><i class="fa-light fa-minus"></i></button>
                                <div><div id="count-{{ $item->product->id }}">{{ $item->quantity }}</div></div>
                                <button class="increase-button" data-code="{{ $item->product->id }}"><i class="fa-light fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="right-side" id="right-side">
        <div id="parser-amount">
            <div class="parser-card-amount">
                <h3>Стоимость заказа</h3>
                <table class="parser-amount-table" style="width: 100%">
                    <tr>
                        <td class="parser-amount-table-caption">Доставка до Калининграда (<span id="weight">{{$cart->weight}}</span> кг)</td>
                        <td class="parser-amount-table-value">
                            <span id="delivery">{{ price($cart->delivery) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="parser-amount-table-caption">Стоимость товаров:</td>
                        <td class="parser-amount-table-value">
                            <span id="amount">{{ price($cart->amount) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td class="parser-amount-table-caption">Итого к оплате:</td>
                        <td class="parser-amount-table-value">
                            <span id="full-amount">{{ price($cart->delivery + $cart->amount) }}</span>
                        </td>
                    </tr>
                </table>

                <div class="parser-card-amount--button">

                    <form id="to-order" method="POST" action="{{ route('shop.order.create-parser') }}">
                        @csrf
                        <button class="btn btn-dark w-100 py-3"  onclick="ym(88113821,'reachGoal','parser-prepare'); return true;"
                                @guest()
                                data-bs-toggle="modal" data-bs-target="#login-popup" type="button"
                                @endguest
                                @auth('user')
                                type="submit"
                            @endauth
                        >Перейти к оформлению
                        </button>
                    </form>
                </div>
            </div>

        </div>
        <div class="parser-info"></div>
    </div>
    </section>
@endsection
