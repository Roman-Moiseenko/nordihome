<div>

    <div id="cart-header" class="dropdown" style="position: relative">
        <a class="nav-link d-flex flex-column text-center dropdown-toggle dropdown-hover"
           href="{{ route('shop.cart.view') }}"
           aria-expanded="false" id="dropdown-cart" aria-haspopup="true">
            <span id="counter-cart" class="counter-cart counter" @if($count == 0) style="display: none;" @endif>{{ $count }}</span>
            <i class="fa-light fa-cart-shopping fs-4"></i>
            <span class="fs-7">Корзина</span>
        </a>
        <div class="dropdown-menu menu-widget-popup" aria-labelledby="dropdown-cart">
            @if($count == 0)
                <div id="cart-empty">
                    У вас нет товаров в корзине {{ $test }} {{ $count }}
                </div>
            @else
                <div id="cart-not-empty">
                    <div class="cart-header">
                        <div>
                            Товаров в корзине <span>{{ $count }}</span>
                        </div>
                        <div>
                            <a id="clear-cart" wire:click="clear_cart" href="#" data-route="{{ route('shop.cart.clear') }}">Очистить корзину</a>
                        </div>
                    </div>
                    <div class="cart-body">
                        @foreach($items as $item)
                        <div id="cart-item-template" class="cart-item">
                            <img class="cart-item-img" src="{{ $item['image'] }}">
                            <div class="cart-item-info">
                                <div class="cart-item-name"><a href="{{ $item['url'] }}" class="cart-item-url" title="{{ $item['name'] }}">{{ $item['name'] }}</a></div>
                                <div class="cart-item-quantity">{{ $item['quantity'] }} шт</div>
                            </div>
                            @if($item['discount_cost'] == 0)
                            <div class="cart-item-cost cart-item-costonly">{{ price($item['cost']) }}</div>
                            @else
                            <div class="cart-item-combined">
                                <div class="cart-item-cost">{{ price($item['cost']) }}</div>
                                <div class="cart-item-discount_cost">{{ price($item['discount_cost']) }}</div>
                            </div>
                            @endif
                            <div class="cart-item-trash">
                                <a href="#" wire:click="del_item({{$item['product_id']}})"><i class="fa-light fa-trash-can"></i></a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="cart-footer">
                        <div class="cart-footer-amount">
                            <div>Итого:</div>
                            @if($discount == 0)
                            <div class="cart-all-amount">
                                <span id="widget-cart-all-amount">{{ price($amount) }}</span>
                            </div>
                            @else
                            <div class="cart-all-combined">
                                <span id="widget-cart-all-discount">{{ price($amount - $discount) }}</span><span id="widget-cart-all-amount-mini">{{ price($amount) }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="cart-footer-button d-flex">
                            <button class="btn btn-outline-dark"
                                    @guest()
                                    data-bs-toggle="modal" data-bs-target="#login-popup"
                                    @endguest
                                    @auth('user')
                                    onclick="document.getElementById('to-order').submit()"
                                @endauth
                            >Оформить</button>
                            <form id="to-order" method="POST" action="{{ route('shop.order.create') }}">
                                @csrf
                            </form>
                            <a class="btn btn-dark ms-2" href="{{ route('shop.cart.view') }}">В корзину</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
