<div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-dark fs-5 ls-1 dropdown-toggle lh-sm" data-bs-toggle="dropdown"
                aria-expanded="false">Каталог&nbsp;
        </button>
        <div class="dropdown-menu">
            <div class="catalog" data-route="{{ route('shop.category.search') }}">
                <div class="catalog-rootmenu" wire:ignore>
                    @foreach($categories as $category)
                        <li wire:ignore.self>
                            <a class="dropdown-item" href="{{ route('shop.category.view', $category['slug']) }}"
                               data-id="{{ $category['id'] }}" >
                                {{ $category['name'] }}
                            </a>
                        </li>
                    @endforeach
                </div>
                <div class="catalog-submenu">
                    <div id="catalog-submenu" class="catalog-submenu-scroll">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
