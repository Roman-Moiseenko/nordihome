<div>
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
                    <div class="product-label promotion"><span>Акция</span></div>
                @endif
                @if($product->isNew())
                    <div class="product-label new"><span>NEW</span></div>
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
                @if(!is_null($user))
                    <button class="{{ $is_wish ? 'is-wish' : 'to-wish' }}" type="button"
                            title="В Избранное" wire:click="toggle_wish">
                        <i class="{{ $is_wish ? 'fa-solid' : 'fa-light' }} fa-heart"></i>
                    </button>
                @else
                    <button class="to-wish" data-bs-toggle="modal" data-bs-target="#login-popup"
                            type="button"
                            onclick="event.preventDefault();">
                        <i class="fa-light fa-heart" type="button" title="В Избранное"></i>
                    </button>
                @endif
            </div>
        </div>
        <div class="product-card-name fs-6">
            <a class="product-trunc" href="{{ route('shop.product.view', $product->slug) }}"
               title="{{ $product->name }}">{{ $product->name }}</a>
        </div>
        <div class="product-card-info">
            @if($product->isSale())
                @if(!$product->hasPromotion())
                    {{ price($product->getPrice()) }}
                @else
                    <span class="discount-price">{{ price($product->promotion()->pivot->price) }}</span><span
                            class="base-price">{{ price($product->getPrice()) }}</span>
                @endif
            @else
                {{ price($product->getPrice()) }}
            @endif
        </div>
        <div class="product-card-to-cart">
            @if($product->isSale())
                <button type="button" class="btn btn-dark" data-product="{{ $product->id }}" wire:click="to_cart"
                        wire:loading.class="loading" wire:loading.attr="disabled">
                    <span class="hide-load">В Корзину</span>
                    <span class="show-load"><i class="fa-sharp fa-light fa-loader"></i></span>
                </button>
                <button class="one-click btn btn-outline-dark"
                        data-product="{{ $product->id }}" type="button" data-bs-toggle="modal"
                        data-bs-target="#buy-click"
                        onclick="document.getElementById('one-click-product-id').value={{$product->id}};"
                >В 1 Клик!
                </button>
            @else
                <button type="button" class="btn btn-secondary" disabled>Снят с продажи</button>
            @endif
        </div>
    </div>
</div>
