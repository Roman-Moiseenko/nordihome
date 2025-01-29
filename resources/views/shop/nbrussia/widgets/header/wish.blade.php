<div id="wish-header" class="dropdown" style="position: relative">
    <a class="nav-link d-flex flex-column text-center dropdown-toggle dropdown-hover"
       href="{{ route('cabinet.wish.index') }}" aria-expanded="false" id="dropdown-wish" aria-haspopup="true"
       @guest('user')
       data-bs-toggle="modal" data-bs-target="#login-popup"
        @endguest
    >
        <span id="counter-wish" class="counter" style="display: none;"></span>
        <i class="fa-light fa-heart fs-4"></i>
        <span class="fs-7">Избранное</span>
    </a>
    @auth('user')
        <div id="wish-block" class="dropdown-menu menu-widget-popup {{ (count($user->wishes) == 0) ? 'hidden' : '' }}"
             aria-labelledby="dropdown-wish">
            <div class="wish-header">
                <div>
                    Товаров в избранном <span id="widget-wish-all-count"></span>
                </div>
                <div>
                    <a id="clear-wish" href="#" data-route="{{ route('cabinet.wish.clear') }}">Очистить избранное</a>
                </div>
            </div>
            <div class="wish-body">
                <div id="wish-item-template" class="wish-item" style="display: none">
                    <img class="wish-item-img" src="">
                    <div class="wish-item-info">
                        <div class="wish-item-name"><a href="" class="wish-item-url"></a></div>
                    </div>
                    <div class="wish-item-cost wish-item-costonly"></div>
                    <div class="wish-item-trash"><a class="remove-item-wish" href="#" data-item="" data-route=""><i
                                class="fa-light fa-trash"></i></a></div>
                </div>
            </div>
        </div>
    @endauth
</div>
