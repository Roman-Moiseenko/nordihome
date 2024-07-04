<div>
    <div class="box-card full-cart-item" id="full-cart-item-{{ $item['product_id'] }}">
        <div class="checked">
            <input type="checkbox" wire:model="check" wire:change="check_item">
        </div>
        <div class="image">
            <a href="{{ $item['url'] }}" target="_blank"><img src="{{ $item['img'] }}"/></a>
        </div>
        <div class="info">
            <div>
                <a href="{{ $item['url'] }}" target="_blank"><span>{{ $item['name'] }}</span></a>
            </div>
            <div class="discount"
                 @if(is_null($item['discount_cost'])) style="display: none" @endif>
                <span class="badge text-bg-danger">{{ $item['discount_name'] }}</span>
            </div>
            <div class="available fs-7 mt-1"
                 @if(is_null($item['available'])) style="display: none" @endif
            > Товар на предзаказ@if(!is_null($item['discount_cost'])) (акция не распространяется)@endif, доступно - <span class="available-count">{{ $item['available'] }}</span> шт.</div>
            <div class="costblock">
                <div class="cost"
                     @if(!is_null($item['discount_cost'])) style="display: none" @endif>
                    <span class="current-cost">{{ price($item['cost']) }}</span>
                </div>
                <div class="combinate"
                     @if(is_null($item['discount_cost'])) style="display: none" @endif>
                    <span class="discount-cost">{{ price($item['discount_cost']) }}</span> <span
                        class="current-cost">{{ price($item['cost']) }}</span>
                </div>
            </div>
        </div>
        <div class="control">
            <div class="set-value">
                <button class="btn btn-outline-dark cartitem-sub" @if($item['quantity'] == 1) disabled @endif
                        wire:click="sub_item" wire:loading.attr="disabled"
                >
                    <i class="fa-light fa-minus"></i></button>
                <input type="text" class="form-control"
                       data-product="{{ $item['product_id'] }}" value="{{ $item['quantity'] }}"
                       wire:change="set_item" wire:model="quantity" wire:loading.attr="disabled"
                />
                <button class="btn btn-outline-dark" wire:click="plus_item" wire:loading.attr="disabled">
                    <i class="fa-light fa-plus"></i>
                </button>
            </div>
            <div class="text-center">
                <span class="current-price">{{ price($item['price']) }}/шт.</span>
            </div>
            <div class="buttons">
                <button class="btn {{ ($wish) ? 'btn-warning' : 'btn-light'  }}" wire:click="toggle_wish">
                    <i class="fa-light fa-heart"></i>
                </button>
                <button class="btn btn-light" wire:click="del_item">
                    <i class="fa-light fa-trash-can"></i>
                </button>
            </div>
        </div>
    </div>
</div>
