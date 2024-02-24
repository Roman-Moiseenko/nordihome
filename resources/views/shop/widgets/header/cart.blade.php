<div id="cart-header" class="dropdown" style="position: relative">
    <a class="nav-link d-flex flex-column text-center dropdown-toggle dropdown-hover"
       href="{{ route('shop.cart.view') }}"
       aria-expanded="false" id="dropdown-cart" aria-haspopup="true">
        <span id="counter-cart" class="counter-cart counter" style="display: none;"></span>
        <i class="fa-light fa-cart-shopping fs-4"></i>
        <span class="fs-7">Корзина</span>
    </a>
    <div class="dropdown-menu menu-widget-popup" aria-labelledby="dropdown-cart">
        <div id="cart-not-empty">
            <div class="cart-header">
                <div>
                    Товаров в корзине <span id="widget-cart-all-count"></span>
                </div>
                <div>
                    <a id="clear-cart" href="#" data-route="{{ route('shop.cart.clear') }}">Очистить корзину</a>
                </div>
            </div>
            <div class="cart-body">
                <div id="cart-item-template" class="cart-item" style="display: none">
                    <img class="cart-item-img" src="">
                    <div class="cart-item-info">
                        <div class="cart-item-name"><a href="" class="cart-item-url"></a></div>
                        <div class="cart-item-quantity"></div>
                    </div>
                    <div class="cart-item-cost cart-item-costonly"></div>
                    <div class="cart-item-combined" style="display: none">
                        <div class="cart-item-cost"></div>
                        <div class="cart-item-discount_cost"></div>
                    </div>
                    <div class="cart-item-trash"><a class="remove-item-cart" href="#" data-item="" data-route=""><i class="fa-light fa-trash-can"></i></a></div>
                </div>
            </div>
            <div class="cart-footer">
                <div class="cart-footer-amount">
                    <div>Итого:</div>
                    <div class="cart-all-amount">
                        <span id="widget-cart-all-amount"></span>
                    </div>
                    <div class="cart-all-combined" style="display: none;">
                        <span id="widget-cart-all-discount"></span><span id="widget-cart-all-amount-mini">[**]</span>
                    </div>
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
        <div id="cart-empty">
            У вас нет товаров в корзине
        </div>
    </div>
</div>
