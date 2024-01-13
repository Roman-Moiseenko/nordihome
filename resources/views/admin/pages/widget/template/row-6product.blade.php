<div class="shop-home-widget">
    <h2 class="fs-4">{{ $widget->title }}</h2>
    <div class="row list-mini-widget">
        @foreach($widget->items as $item)
            <div class="col-lg-2">
                <a href="{{ $item['url'] }}" title="{{ $item['title'] }}">
                    <img src="{{ $item['image']->getThumbUrl('promotion-mini') }}" class="p-2" alt="{{ $item['title'] }}"/>
                    <div class="price-block">
                        <span class="price">{{ price($item['price']) }}</span>
                    </div>
                </a>

            </div>
        @endforeach
    </div>
</div>
