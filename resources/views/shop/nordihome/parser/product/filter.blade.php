<div class="filters">
    <div class="mobile-close"><i class="fa-light fa-xmark"></i></div>
    <div class="base-filter">
        <div class="children">
            @if(!is_null($category->parent))
                <div>
                    <a href="{{ route('shop.parser.catalog', $category->parent->slug) }}">Назад</a>
                </div>
            @endif
            @foreach($children as $child)
                <div>
                    <a
                        href="{{ route('shop.parser.catalog', $child['slug']) }}"
                        class="{{ isset($category) ? ($child['id'] == $category->id ? 'active' : '') : '' }}"
                    >{{ $child['name'] }}</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
