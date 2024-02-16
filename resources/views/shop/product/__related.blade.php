@if(!empty($related))
    <div class="view-related">
        <h4>Аксессуары</h4>
        <div class="slider-images-product owl-carousel owl-theme" data-responsive="[2,3,4]">
            @foreach($related as $_product)
                <div class="item-slider">
                    <a href="{{ route('shop.product.view', $_product->slug) }}" title="{{ $_product->name }}">
                        <img src="{{ $_product->photo->getThumbUrl('thumb') }}" alt="{{ $_product->photo->alt }}">
                    </a>
                    <div>{{ price($_product->lastPrice->value) }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif
