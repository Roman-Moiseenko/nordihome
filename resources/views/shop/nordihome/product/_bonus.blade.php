@if($bonus->count() > 0)
    <div class="box-card view-bonus">
        <h3 id="bonus">Выгодная покупка</h3>
        <div class="d-flex justify-content-around">
            @foreach($bonus as $_product)
                <div class="item-bonus px-2">
                    <a href="{{ route('shop.product.view', $_product->slug) }}" title="{{ $_product->name }}">
                        <img src="{{ $_product->getImage('thumb') }}" alt="{{ $_product->name }}">
                    </a>
                    <div class="price-block">
                        <div class="discount-price">{{ price($_product->pivot->discount) }}</div>
                        <div class="base-price">{{ price($_product->getLastPrice()) }}</div>
                    </div>
                    <button class="to-cart btn btn-dark" data-product="{{ $_product->id }}">В Корзину</button>
                </div>
            @endforeach
        </div>
        <div class="fs-8 mt-3">
            * Бонусная покупка работает при условии одинакового количества основного и бонусного товара в корзине.
        </div>
    </div>
@endif
