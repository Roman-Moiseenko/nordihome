<!--template:Акция - 4 товара-->
<div class="container-xl shop-home-widget">
    <div class="row promotion-widget">
        <div class="col-lg-3">
            <a href="{{ $widget->url }}" title="{{ $widget->title }}">
                <img src="{{ $widget->image->getThumbUrl('promotion') }}"/>
            </a>
        </div>
        <div class="col-lg-9">
            <div class="py-2 ps-2"><a href="{{ $widget->url }}" title="{{ $widget->title }}">
                    <h4>{{ $widget->title }}</h4></a></div>
            <div class="row">
                @for($i = 0; $i < 4; $i++)
                    <div class="col-lg-3">
                        <a href="{{ $widget->items[$i]['url'] }}" title="{{ $widget->items[$i]['title'] }}">
                            <img class="p-2" src="{{ $widget->items[$i]['image']->getThumbUrl('promotion') }}"
                                 alt="{{ $widget->items[$i]['title'] }}"/>
                        </a>
                        <div class="py-2 ps-2">
                            <span class="discount-price">{{ price($widget->items[$i]['discount']) }}</span>
                            <span class="base-price">{{ price($widget->items[$i]['price']) }}</span>
                            <div class="fs-7 pt-1 count-product">Осталось {{ $widget->items[$i]['count'] }} шт.</div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
