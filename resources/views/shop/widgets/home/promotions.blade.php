<div class="row promotion-widget">
    <div class="col-lg-3">
        <a href="{{ $items[0]['url'] }}" title="{{ $items[0]['title'] }}">
            <img src="{{ $items[0]['image'] }}" />
        </a>
    </div>
    <div class="col-lg-9">
        <div class="py-2 ps-2"><a href="{{ $items[0]['url'] }}" title="{{ $items[0]['title'] }}"><h4>{{ $items[0]['title'] }}</h4></a></div>
        <div class="row">
            @for($i = 1; $i <= 4; $i++)
            <div class="col-lg-3">
                <a href="{{ $items[$i]['url'] }}" title="{{ $items[$i]['title'] }}">
                    <img class="p-2" src="{{ $items[$i]['image'] }}" alt="{{ $items[$i]['title'] }}"/>
                </a>
                <div class="py-2 ps-2">
                    <span class="discount-price">{{ price($items[$i]['discount']) }}</span>
                    <span class="base-price">{{ price($items[$i]['price']) }}</span>
                    <div class="fs-7 pt-1 count-product">Осталось {{ $items[$i]['count'] }} шт.</div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
