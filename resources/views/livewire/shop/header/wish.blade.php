<div>
    <div id="wish-header" class="dropdown" style="position: relative">
        <a class="nav-link d-flex flex-column text-center dropdown-toggle dropdown-hover"
           href="{{ route('cabinet.wish.index') }}" aria-expanded="false" id="dropdown-wish" aria-haspopup="true"
           @if(is_null($user))
           data-bs-toggle="modal" data-bs-target="#login-popup"
            @endif
        >
            <span id="counter-wish" class="counter" @if($count == 0) style="display: none;" @endif>{{ $count }}</span>
            <i class="fa-light fa-heart fs-4"></i>
            <span class="fs-7">Избранное</span>
        </a>
        @if(!is_null($user))
            <div id="wish-block" class="dropdown-menu menu-widget-popup {{ ($count == 0) ? 'hidden' : '' }}"
                 aria-labelledby="dropdown-wish">
                <div class="wish-header">
                    <div>
                        Товаров в избранном <span id="widget-wish-all-count">{{ $count }}</span>
                    </div>
                    <div>
                        <button id="clear-wish" href="#" data-route="{{ route('cabinet.wish.clear') }}" wire:click="remove_all">Очистить избранное</button>
                    </div>
                </div>
                <div class="wish-body">
                    @foreach($items as $item)
                    <div id="wish-item-template" class="wish-item">
                        <img class="wish-item-img" src="{{ $item['image'] }}">
                        <div class="wish-item-info">
                            <div class="wish-item-name"><a href="{{ $item['url'] }}" class="wish-item-url">{{ $item['name'] }}</a></div>
                        </div>
                        <div class="wish-item-cost wish-item-costonly"></div>
                        <div class="wish-item-trash"><button wire:click="remove({{ $item['id'] }})"><i
                                    class="fa-light fa-trash"></i></button></div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
