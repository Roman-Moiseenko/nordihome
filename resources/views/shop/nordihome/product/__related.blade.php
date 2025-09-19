@if(count($related) > 0)
    <div class="view-related">
        <h4>Аксессуары</h4>
        <div class="slider-images-product owl-carousel owl-theme" data-responsive="[2,3,4]">
            @foreach($related as $_product)
                <div class="item-slider">
                    <a
                        href="{{ route('shop.product.view', $_product['slug']) }}" title="{{ $_product['name'] }}"
                       class="dropdown-toggle dropdown-hover" aria-expanded="false" id="dropdown-related" aria-haspopup="true">
                        <img src="{{ $_product['image']['src'] }}" alt="{{ $_product['name']}}">
                    </a>
                    <div class="item-price">{{ price($_product['price']) }}</div>
                    <div class="button">
                        <button class="to-cart btn btn-dark" data-product="{{ $_product['id'] }}">В Корзину</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
