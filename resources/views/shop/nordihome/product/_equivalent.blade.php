@if($equivalents != null)
    <div class="box-card">
        <h3 id="equivalent">Похожие товары</h3>
        <div class="slider-images-product owl-carousel owl-theme" data-responsive="[2,4,6]" data-mouse-scroll="0">
            @foreach($equivalents as $_product)
                <div class="px-1">
                    <a href="{{ route('shop.product.view', $_product['slug']) }}" title="{{ $_product['name'] }}">
                        <img src="{{ $_product['src'] }}" alt="{{ $_product['name'] }}">
                    </a>
                    <a href="{{ route('shop.product.view', $_product['slug']) }}" title="{{ $_product['name'] }}">
                        <span class="fs-8 product-trunc">
                            {{ $_product['name'] }}
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
