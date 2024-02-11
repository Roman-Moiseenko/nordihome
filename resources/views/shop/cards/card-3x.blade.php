<div class="col-6 col-sm-4 col-lg-3 mb-5">
    <div class="product-card">
        <div class="product-card-image">
            <a href="{{ route('shop.product.view', $product->slug) }}">
                <img class="product-card-image-main"
                    src="{{ (is_null($product->photo)) ? '/images/no-image.jpg' : $product->photo->getThumbUrl('catalog-watermark') }}"
                    alt="{{ empty($product->photo->alt) ? $product->name : $product->photo->alt }}">
                <img class="product-card-image-hover"
                     src="{{ (is_null($product->photo_next())) ? '/images/no-image.jpg' : $product->photo_next()->getThumbUrl('catalog-watermark') }}"
                     alt="{{ empty($product->photo_next()->alt) ? $product->name : $product->photo_next()->alt }}">

                @if(!is_null($product->isPromotion()))
                    <div class="promotion-label"><span>Акция</span></div>
                @endif
            </a>
        </div>
        <div class="product-card-review">
            <div>
                <a href="{{ route('shop.product.view', $product->slug) }}/#review">
                    <i class="fa-solid fa-star"></i>{{ $product->current_rating }} <span
                        class="">{{ $product->countReviews() }}</span>
                </a>
            </div>
            <div>
                @if(!is_null($user))
                    <a class="product-wish-toggle
                {{ $product->isWish($user->id) ? 'is-wish' : 'to-wish' }}" data-product="{{ $product->id }}"
                       type="button" title="В Избранное"><i
                            class="{{ $product->isWish($user->id) ? 'fa-solid' : 'fa-light' }} fa-heart"></i></a>
                @else
                    <a class="to-wish" data-bs-toggle="modal" data-bs-target="#login-popup"
                       onclick="event.preventDefault();"><i
                            class="fa-light fa-heart" type="button" title="В Избранное"></i></a>
                @endif
            </div>
        </div>
        <div class="product-card-name fs-6">
            <a class="product-trunc" href="{{ route('shop.product.view', $product->slug) }}"
               title="{{ $product->name }}">{{ $product->name }}</a>
        </div>
        <div class="product-card-info">
            @if(is_null($product->isPromotion()))
                {{ price($product->lastPrice->value) }}
            @else
                <span class="discount-price">{{ price($product->isPromotion()['price']) }}</span><span class="base-price">{{ price($product->lastPrice->value) }}</span>
            @endif


        </div>
        <div class="product-card-to-cart">
            <button class="to-cart btn btn-dark" data-product="{{ $product->id }}">В Корзину</button>
            <button class="to-cart btn btn-outline-dark" data-product="{{ $product->id }}">В 1 Клик!</button>
        </div>
    </div>
</div>
