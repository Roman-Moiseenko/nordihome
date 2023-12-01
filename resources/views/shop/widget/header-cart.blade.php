<div class="dropdown" style="position: relative">
    <a class="nav-link d-flex flex-column text-center dropdown-toggle dropdown-hover" href="{{ route('shop.cart.view') }}"
       aria-expanded="false" id="dropdown-cart" aria-haspopup="true">
        <i class="fa-light fa-cart-shopping fs-4"></i>
        <span class="fs-7">Корзина</span>
    </a>
    <div class="dropdown-menu cart-popup" aria-labelledby="dropdown-cart">
        <div class="cart-header">
            <div>
                Товаров в корзине <span id="widget-cart-all-count">[*]</span>
            </div>
            <div>
                <a href="#">Очистить корзину</a>
            </div>
        </div>
        <div class="cart-body">
            <div class="cart-item">
                <img src="\images\no-image.jpg">
                <div class="cart-item-info">
                    <div class="cart-item-name">Название товара с кодами и артикулами</div>
                    <div class="cart-item-quantity">5 шт</div>
                </div>
                <div class="cart-item-cost">10 000 ₽</div>
                <div class="cart-item-trash"><i class="fa-light fa-trash-can"></i></div>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-footer-amount">
                <div>Итого:</div>
                <div><span id="widget-cart-all-amount">[**]</span></div>
            </div>
            <div class="cart-footer-button">
                <a class="btn btn-outline-dark"href="{{ route('shop.cart.view') }}">Оформить</a>
                <a class="btn btn-dark" href="{{ route('shop.cart.view') }}">В корзину</a>
            </div>
        </div>

    </div>
</div>
