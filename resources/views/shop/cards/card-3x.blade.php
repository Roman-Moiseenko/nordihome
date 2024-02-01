<div class="col-sm-6 col-lg-4 mt-3">
    <div class="card">
        <div class="product-card-image">
            <a href="{{ route('shop.product.view', $product->slug) }}">
                <img
                    src="{{ (is_null($product->photo)) ? '/images/no-image.jpg' : $product->photo->getThumbUrl('card') }}"
                    alt="{{ $product->name }}">
            </a>
        </div>
        <div class="product-card-name">
            <span class="fs-5">
                <a href="{{ route('shop.product.view', $product->slug) }}">{{ $product->name }}</a>
            </span>
        </div>
        <div class="product-card-info">Рейтинг, цена</div>
        <div class="product-card-to-cart">
            @if(!is_null($user_id))
                <button class="product-wish-toggle btn
                {{ $product->isWish($user_id) ? 'btn-warning' : 'btn-outline-dark' }}" data-product="{{ $product->id }}" type="button"><i
                        class="fa-light fa-heart"></i></button>
            @else
                    <button class="btn btn-outline-light"  data-bs-toggle="modal" data-bs-target="#login-popup" onclick="event.preventDefault();"><i
                            class="fa-light fa-heart" type="button"></i></button>
            @endif
            <button class="to-cart btn btn-dark" data-product="{{ $product->id }}">В Корзину</button>
        </div>
    </div>
</div>
