<div class="row list-big-widget">
    @foreach($items as $item)
        <div class="col-lg-3">
            <a href="{{ $item['url'] }}" title="{{ $item['title'] }}">
                <img src="{{ $item['image'] }}" class="px-2" alt="{{ $item['title'] }}"/>
                <div class="price-block">
                    <span class="price">{{ price($item['price']) }}</span>
                </div>
            </a>
        </div>
    @endforeach
</div>
