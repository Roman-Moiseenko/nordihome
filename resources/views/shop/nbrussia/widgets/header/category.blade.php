<ul class="menu-category">

    @foreach($tree as $category)
        <li class="menu-item">
            <a class="dropdown-item" href="{{ route('shop.category.view', $category->slug) }}"
               data-id="{{ $category->id }}">
                {{ $category->name }}
            </a>
            <div class="sub-menu">
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-2">
                        <h4 class="mt-2">Новинки</h4>


                    </div>
                    @foreach($category->children as $child)
                        <div class="col-2">
                            <h4 class="mt-2">{{ $child->name }}</h4>
                            @foreach($child->children as $sub)
                                <div>
                                    <a class="" href="{{ route('shop.category.view', $sub->slug) }}"
                                       data-id="{{ $sub->id }}">
                                        {{ $sub->name }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </li>
    @endforeach

    <div class="dropdown-menu">
        <div class="catalog" data-route="{{ route('shop.category.search') }}">
            <div class="catalog-rootmenu">
                @foreach($categories as $category)
                    <li>
                        <a class="dropdown-item" href="{{ route('shop.category.view', $category['slug']) }}"
                           data-id="{{ $category['id'] }}">

                            {{ $category['name'] }}
                        </a>
                    </li>
                @endforeach
            </div>
            <div class="catalog-submenu">
                <div id="catalog-submenu" class="catalog-submenu-scroll"></div>
            </div>
        </div>
    </div>
</ul>
