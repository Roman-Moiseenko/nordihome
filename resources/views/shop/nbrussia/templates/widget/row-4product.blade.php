<!--template:Построчно 4 товара-->
<div class="container-xl shop-home-widget">
    <h2 class="fs-4">{{ $widget->title }}</h2>
    <div class="row list-big-widget">
        @foreach($widget->items as $item)
            <div class="col-lg-3">
                <a href="{{ $item['url'] }}" title="{{ $item['title'] }}">
                    <img src="{{ $item['image']->getThumbUrl('promotion') }}" class="px-2" alt="{{ $item['title'] }}"/>
                    <div class="price-block">
                        <span class="price">{{ price($item['price']) }}</span>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
