@php
    use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
     /** @var ProductCardData $product */
@endphp

<div>
    <div class="product-card" data-product="{{ $product->id }}">
        <div class="product-card-image">
            <a class="e-click" data-product="{{ $product->id }}"
               href="{{ route('shop.ikea.product', $product->slug) }}">
                <img class="product-card-image-main"
                     src="{{ $product->image->src }}"
                     alt="{{ $product->image->alt }}">
                <img class="product-card-image-hover"
                     src="{{ $product->image_next->src }}"
                     alt="{{ $product->image_next->alt }}">
            </a>
        </div>

        <div class="product-card-name fs-6">
            <a class="product-trunc" data-product="{{ $product->id }}"
               href="{{ route('shop.ikea.product', $product->slug) }}"
               title="{{ $product->name }}">{{ $product->name }}</a>
        </div>
        <div>
            <span>Артикул: <span>{{ $product->code }}</span></span>
        </div>
        <div>
            <span>{{ $product->short }}</span>
        </div>
        <div class="product-card-info">
            {{ price($product->price) }}
        </div>

        <div>
            <button class="parser-to-cart">В корзину</button>
        </div>
    </div>
</div>
