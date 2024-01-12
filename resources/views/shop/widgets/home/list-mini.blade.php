<div class="row list-mini-widget">
    @foreach($items as $item)
        <div class="col-lg-2">
            <a href="{{ $item['url'] }}" title="{{ $item['title'] }}">
                <img src="{{ $item['image'] }}" class="p-2" alt="{{ $item['title'] }}"/>
                <div class="price-block">
                    <span class="price">{{ price($item['price']) }}</span>
                </div>
            </a>

        </div>
    @endforeach
</div>
