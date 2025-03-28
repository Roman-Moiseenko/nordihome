<div class="container-xl shop-home-widget">
    <a href="{{ $widget->url }}" title="{{ $widget->title }}">
        <h2 class="fs-4">{{ $widget->title }} <i class="fa-sharp fa-light fa-circle-arrow-right"></i></h2>

    </a>
    <div class="row list-mini-widget">
        @foreach(array_slice($widget->items, 0, 18) as $item)
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
