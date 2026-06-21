<div>
    <div class="product-card --e-impressions" data-product="{{ $product['id'] }}">
        <div class="product-card-image">
            <a class="--e-click" data-product="{{ $product['id'] }}" href="{{ route('shop.parser.product', $product['slug']) }}">
                <img class="product-card-image-main"
                     src="{{ $product['images']['catalog']['src'] }}"
                     alt="{{ $product['images']['catalog']['alt'] }}">
                <img class="product-card-image-hover"
                     src="{{ $product['images-next']['catalog']['src'] }}"
                     alt="{{ $product['images-next']['catalog']['alt'] }}">

            </a>
        </div>
        <div class="product-card-review">
        </div>
        <div class="product-card-name fs-6">
            <a class="product-trunc --e-click" data-product="{{ $product['id'] }}" href="{{ route('shop.parser.product', $product['slug']) }}"
               title="{{ $product['name'] }}">{{ $product['name'] }}</a>
        </div>
        <div class="product-card-info">
                {{ price($product['price']) }}
        </div>
    </div>
</div>
