<div>
    <a href="{{ route('shop.cart.view') }}"
       class="nav-link d-flex flex-column text-center position-relative">
        <span id="counter-cart" class="counter-cart counter" @if($count == 0) style="display: none;" @endif>{{ $count }}</span>
        <i class="fa-light fa-cart-shopping fs-3"></i>
        <span class="fs-8">Корзина</span>
    </a>
</div>
