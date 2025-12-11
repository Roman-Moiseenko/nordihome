<div class="filters">
    <div class="mobile-close"><i class="fa-light fa-xmark"></i></div>
    <div class="base-filter">
        <div class="children">
            @foreach($children as $child)
                <div>
                    <a
                        href="{{ route('shop.category.view', $child['slug']) }}"
                        class="{{ isset($category) ? ($child['id'] == $category->id ? 'active' : '') : '' }}"
                    >{{ $child['name'] }}</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
