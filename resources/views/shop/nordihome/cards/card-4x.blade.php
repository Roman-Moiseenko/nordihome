<div class="col-6 col-sm-4 col-lg-3 mb-5">
    <div class="product-card">
        <div class="product-card-image">
            <a href="{{ route('shop.product.view', $product->slug) }}">
                <img class="product-card-image-main"
                     src="{{ $product->getImage('catalog-watermark') }}"
                     alt="{{ $product->name }}">
                <img class="product-card-image-hover"
                     src="{{ $product->getImageNext('catalog-watermark') }}"
                     alt="{{ $product->name }}">

                @if($product->hasPromotion())
                    <div class="promotion-label"><span>Акция</span></div>
                @endif
            </a>
        </div>
        <div class="product-card-review">
            <div>
                <a href="{{ route('shop.product.view', $product->slug) }}/#review"
                   title="Отзывы реальных покупателей на {{ $product->name }}">
                    <i class="fa-solid fa-star"></i>{{ $product->current_rating }} <span
                            class="">{{ $product->countReviews() }}</span>
                </a>
            </div>
            <div>
                @include('shop.nordihome.widgets.to-wish', ['product' => $product])
            </div>
        </div>
        <div class="product-card-name fs-6">
            <a class="product-trunc" href="{{ route('shop.product.view', $product->slug) }}"
               title="{{ $product->name }}">{{ $product->name }}</a>
        </div>
        <div class="product-card-info">
            @if(!$product->hasPromotion())
                {{ price($product->getPrice()) }}
            @else
                <span class="discount-price">{{ price($product->promotion()->pivot->price) }}</span><span
                        class="base-price">{{ price($product->getPrice()) }}</span>
            @endif
        </div>
        <div class="product-card-to-cart">
            <button class="to-cart btn btn-dark" data-product="{{ $product->id }}">В Корзину</button>
            <button class="one-click btn btn-outline-dark"
                    data-product="{{ $product->id }}" type="button" data-bs-toggle="modal" data-bs-target="#buy-click"
                    onclick="document.getElementById('one-click-product-id').value={{$product->id}};"
            >В 1 Клик!
            </button>
        </div>
    </div>
</div>
