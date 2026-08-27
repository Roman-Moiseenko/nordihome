@php
    use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
    /** @var ProductCardData $product */
@endphp

<div>
    <div class="product-card e-impressions" data-product="{{ $product->id }}">
        <div class="product-card-image">
            <a class="e-click" data-product="{{ $product->id }}"
               href="{{ route('shop.product.view', $product->slug) }}">
                <img class="product-card-image-main"
                     src="{{ $product->image->src }}"
                     alt="{{ $product->image->alt }}">
                <img class="product-card-image-hover"
                     src="{{ $product->image_next->src }}"
                     alt="{{ $product->image_next->alt }}">

                @if($product->is_new)
                    <div class="product-label new"><span>NEW</span></div>
                @elseif($product->promotion->has)
                    <div class="product-label promotion {{ $product->promotion->color }}">
                        <span>{{ $product->promotion->text }}</span>
                    </div>
                @endif

            </a>
        </div>
        <div class="product-card-review">
            <div>
                <a class="e-click" data-product="{{ $product->id }}"
                   href="{{ route('shop.product.view', $product->slug) }}/#review"
                   title="Отзывы реальных покупателей на {{ $product->name }}">
                    <i class="fa-solid fa-star"></i>{{ $product->rating }} <span
                        class="">{{ $product->count_reviews }}</span>
                </a>
            </div>
            <div>
                @if(!is_null($client))
                    <button class="{{ $product->is_wish ? 'is-wish' : 'to-wish' }}" type="button"
                            title="В Избранное" data-product="{{ $product->id }}">
                        <i class="{{ $product->is_wish  ? 'fa-solid' : 'fa-light' }} fa-heart"></i>
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
        <div class="product-card-name">
            <a class="product-trunc e-click" data-product="{{ $product->id }}"
               href="{{ route('shop.product.view', $product->slug) }}"
               title="{{ $product->name }}">{{ $product->name }}</a>
        </div>
        <div class="short-description">тут краткое описание</div>
        <div class="product-card-info">
            @if($product->is_sale)
                @if(!$product->promotion->has)
                    {{ price($product->price) }}
                @else
                    <span class="discount-price">{{ price($product->promotion->price) }}</span><span
                        class="base-price">{{ price($product->price) }}</span>
                @endif
            @else
                {{ price($product->price) }}
            @endif
        </div>
        <div>
            <button class="to-cart btn btn-black e-add{{ $product->in_cart ? ' in-cart' : '' }}" data-product="{{ $product->id }}">
                {{ $product->in_cart ? 'В Корзине' : 'В Корзину' }}
            </button>
        </div>

    </div>
</div>
