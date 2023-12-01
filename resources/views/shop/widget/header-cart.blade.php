<div id="cart-header" class="dropdown" style="position: relative">
    <a class="nav-link d-flex flex-column text-center dropdown-toggle dropdown-hover"
       href="{{ route('shop.cart.view') }}"
       aria-expanded="false" id="dropdown-cart" aria-haspopup="true">
        <span id="counter-cart" style="display: none;"></span>
        <i class="fa-light fa-cart-shopping fs-4"></i>
        <span class="fs-7">Корзина</span>
    </a>
    <div class="dropdown-menu cart-popup" aria-labelledby="dropdown-cart">
        <div id="cart-not-empty">
            <div class="cart-header">
                <div>
                    Товаров в корзине <span id="widget-cart-all-count">[*]</span>
                </div>
                <div>
                    <a id="clear-cart" href="#" data-route="{{ route('shop.cart.clear') }}">Очистить корзину</a>
                </div>
            </div>
            <div class="cart-body">
                <div id="cart-item-template" class="cart-item" style="display: none">
                    <img src="{img}">
                    <div class="cart-item-info">
                        <div class="cart-item-name"><a href="{url}">{name}</a></div>
                        <div class="cart-item-quantity">{quantity} шт</div>
                    </div>
                    <div class="cart-item-cost">{cost} ₽</div>
                    <div class="cart-item-combined" style="display: none">
                        <div class="cart-item-base_cost">{cost} ₽</div>
                        <div class="cart-item-discount_cost">{discount_cost} ₽</div>
                    </div>
                    <div class="cart-item-trash"><a class="remove-item-cart" href="#" data-item="{id}" data-route="{remove}"><i class="fa-light fa-trash-can"></i></a></div>
                </div>
            </div>
            <div class="cart-footer">
                <div class="cart-footer-amount">
                    <div>Итого:</div>
                    <div><span id="widget-cart-all-amount">[**]</span></div>
                </div>
                <div class="cart-footer-button">
                    <a class="btn btn-outline-dark" href="{{ route('shop.cart.view') }}">Оформить</a>
                    <a class="btn btn-dark" href="{{ route('shop.cart.view') }}">В корзину</a>
                </div>
            </div>
        </div>
        <div id="cart-empty">
            У вас нет товаров в корзине
        </div>
    </div>
</div>
