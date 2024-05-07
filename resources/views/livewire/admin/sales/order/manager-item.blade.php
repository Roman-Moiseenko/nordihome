<div>
    <div class="box flex items-center p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-1/4">
            <div>{{ $item->product->name }}</div>
            <div>{{ $item->product->dimensions->weight() }} кг | {{ $item->product->dimensions->volume() }} м3</div>
        </div>
        <div class="w-32 text-center px-1">
            <div>{{ price($item->base_cost) }}</div>

            <input type="number" class="form-control text-center"
                   min="0" @if(!$edit || ($item->product->hasPromotion() && $item->preorder == false)) readonly @endif
                   wire:change="set_sell" wire:model="sell_cost" wire:loading.attr="disabled"
            >

        </div>
        <div class="w-20 text-center px-1">
            <div>%</div>
            <input type="text" class="form-control text-center"
                   min="0" max="50" @if(!$edit || ($item->product->hasPromotion() && $item->preorder == false)) readonly @endif
                   wire:change="set_percent" wire:model="sell_percent" wire:loading.attr="disabled"
            >
        </div>

        <div class="w-20 px-1 text-center">
            <div>{{  $edit ? (($item->product->getCountSell() + $item->quantity) . ' шт.') : '-' }} </div>

            <input type="number" class="form-control text-center"
                   wire:change="set_quantity" wire:model="quantity" wire:loading.attr="disabled"
                   min="1" @if(!$item->preorder) max="{{ $item->product->getCountSell() + $item->quantity }}"
                   @endif @if(!$edit) readonly @endif
            >

        </div>
        <div class="w-20 text-center">
            {{ $item->product->getCountSell() }}
        </div>
        <div class="w-20 text-center">
            <div class="form-check form-switch justify-center mt-3">
                <input id="assemblage-{{ $item->id }}"
                       class="form-check-input" type="checkbox" name="delivery"
                       @if(!$edit) disabled @endif
                       wire:change="check_assemblage" wire:model="assemblage" wire:loading.attr="disabled"
                >
                <label class="form-check-label" for="assemblage-{{ $item->id }}"></label>
            </div>
        </div>
        <div class="w-40 text-left">
            <input type="text" class="form-control" wire:change="set_comment" wire:model="comment" wire:loading.attr="disabled" />
        </div>

        <div class="w-20 text-center">
            <button class="btn btn-outline-danger ml-6 product-remove" type="button"
                wire:click="delete"
                wire:confirm="Удалить позицию из заказа?"
            >X</button>
        </div>
    </div>
</div>
