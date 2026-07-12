@php
    use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
     /** @var ProductCardData $product */
@endphp

<div>
    <div class="product-card e-impressions" data-product="{{ $product->id }}">
        <div class="product-card-image">
            <a class="e-click" data-product="{{ $product->id }}"
               href="{{ route('shop.ikea.product', $product->slug) }}">
                <img class="product-card-image-main"
                     src="{{ $product->image->src }}"
                     alt="{{ $product->image->alt }}">
                <img class="product-card-image-hover"
                     src="{{ $product->image_next->src }}"
                     alt="{{ $product->image_next->alt }}">

                @if($product->promotion->has)
                    <div class="product-label promotion"><span>Акция</span></div>
                @endif
                @if($product->is_new)
                    <div class="product-label new"><span>Новинка</span></div>
                @endif
            </a>
        </div>

        <div class="product-card-name fs-6">
            <a class="product-trunc e-click" data-product="{{ $product->id }}"
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

    </div>
</div>
