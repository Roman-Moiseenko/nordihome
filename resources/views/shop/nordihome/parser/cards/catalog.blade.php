<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="catalog-card">
        <a href="{{ route('shop.parser.catalog', $category['slug']) }}">
            <div>
                <img
                    src="{{ (is_null($category['image'])) ? '\images\no-image.jpg' : $category['image'] }}">
                <span>{{ $category['name'] }}</span>
            </div>
        </a>
        @if(isset($category['children']))
            <ul>
                @foreach($category['children'] as $child)
                    <li>
                        <a href="{{ route('shop.parser.catalog', $child['slug']) }}">{{ $child['name'] }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
