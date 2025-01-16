@if(!empty($series))
    <div class="box-card">
        <h3 id="series">Все товары серии {{ $series->name }}</h3>
        <div class="slider-images-product owl-carousel owl-theme" data-responsive="[3,6,9]" data-mouse-scroll="0">
            @foreach($series->products as $_product)
                <div class="px-1">
                    <a href="{{ route('shop.product.view', $_product->slug) }}" title="{{ $_product->name }}">
                        <img src="{{ $_product->photo->getThumbUrl('thumb') }}" alt="{{ $_product->photo->alt }}">
                    </a>
                    <a href="{{ route('shop.product.view', $_product->slug) }}" title="{{ $_product->name }}">
                            <span class="fs-8 product-trunc">
                                {{ $_product->name }}
                            </span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
