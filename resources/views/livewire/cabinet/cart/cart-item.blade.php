<div>
    <div class="box-card full-cart-item" id="full-cart-item-{{ $item['productId'] }}">
        <div class="checked">
            <input type="checkbox" wire:model="check" wire:change="check_item">
        </div>
        <div class="image">
            <a href="{{ $item['url'] }}" target="_blank"><img src="{{ $item['image'] }}"/></a>
        </div>
        <div class="info">
            <div>
                <a href="{{ $item['url'] }}" target="_blank"><span>{{ $item['name'] }}</span></a>
            </div>
            <div class="discount"
                 @if(is_null($item['discountPrice'])) style="display: none" @endif>
                <span class="badge text-bg-danger">{{ $item['discountName'] }}</span>
            </div>
            <div class="available fs-7 mt-1"
                 @if(!$item['isParser']) style="display: none" @endif
            > Товар на доставку из Икеа@if(!is_null($item['discountPrice'])) (акции не распространяются)@endif</div>
            <div class="costblock">
                <div class="cost"
                     @if($item['discountPrice'] != 0) style="display: none" @endif>
                    <span class="current-cost">{{ price($item['cost']) }}</span>
                </div>
                <div class="combinate"
                     {{ $item['discountPrice'] }}
                     @if($item['discountPrice'] == 0) style="display: none" @endif>
                    <span class="discount-cost">{{ price($item['discountPrice']) }}</span> <span
                        class="current-cost">{{ price($item['cost']) }}</span>
                </div>
            </div>
        </div>
        <div class="control">
            <div class="set-value">
                <button class="btn btn-outline-dark cartitem-sub"
                        @if($item['quantity'] == 1) disabled @endif
                        wire:click="sub_item" wire:loading.attr="disabled"
                >
                    <i class="fa-light fa-minus"></i></button>
                <input type="text" class="form-control" autocomplete="off"
                       data-product="{{ $item['productId'] }}" value="{{ $item['quantity'] }}"
                       wire:change="set_item" wire:model="quantity" wire:loading.attr="disabled"
                />
                <button class="btn btn-outline-dark"
                        wire:click="plus_item" wire:loading.attr="disabled">
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
