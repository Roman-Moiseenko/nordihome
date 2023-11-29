<div class="col-sm-6 col-lg-4 mt-3">
    <div class="card">
        <div class="product-card-image">
            <a href="{{ route('shop.product.view', $product->slug) }}"><img src="{{ $product->getImage() }}" alt="{{ $product->name }}"></a>
        </div>
        <div class="product-card-name">
            <span class="fs-5">
                <a href="{{ route('shop.product.view', $product->slug) }}">{{ $product->name }}</a>
            </span>
        </div>
        <div class="product-card-info">Рейтинг, цена</div>
        <div class="product-card-to-cart"><button class="to-cart btn btn-dark" data-product="{{ $product->id }}">В Корзину</button></div>
    </div>
</div>
