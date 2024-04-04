<div class="box flex items-center p-2">
    <div class="w-20 text-center">{{ $i + 1 }}</div>
    <div class="w-1/4">
        <div>{{ $item->product->name }}</div>
        <div>{{ $item->product->dimensions->weight() }} кг | {{ $item->product->dimensions->volume() }} м3</div>
    </div>
    <div class="w-32 text-center px-1">
        <div>{{ price($item->base_cost) }}</div>

            <input id="sell_cost-{{ $item->id }}" type="text" class="form-control text-center update-data-ajax"
                   value="{{ $item->sell_cost }}" aria-describedby="input-sell_cost"
                   min="0" data-id="{{ $item->id }}" @if(!$edit || ($item->product->hasPromotion() && $item->preorder == false)) readonly @endif
                   data-route="{{ route('admin.sales.order.update-sell', $item) }}"
            >

    </div>
    <div class="w-20 px-1 text-center">
        <div>{{  $edit ? (($item->product->count_for_sell + $item->quantity) . ' шт.') : '-' }} </div>

        <input id="quantity-{{ $item->id }}" type="number" class="form-control text-center update-data-ajax"
               value="{{ $item->quantity }}" aria-describedby="input-quantity"
               min="1" @if(!$item->preorder) max="{{ $item->product->count_for_sell + $item->quantity }}"
               @endif data-id="{{ $item->id }}" @if(!$edit) readonly @endif
               data-route="{{ route('admin.sales.order.update-quantity', $item) }}"
        >

    </div>
    <div class="w-40 text-center">
        @foreach($item->product->getStorages() as $storage)
            {{ $storage->getQuantity($item->product) . '(' . $storage->name . ')' }}<br>
        @endforeach
    </div>
    <div class="w-20 text-center">
        <div class="form-check form-switch justify-center mt-3">
            <input id="delivery-{{ $item->id }}" class="form-check-input update-data-ajax" type="checkbox" name="delivery"
                   @if(!$edit) disabled @endif @if($item->assemblage) checked @endif
                   data-route="{{ route('admin.sales.order.check-assemblage', $item) }}"
            >
            <label class="form-check-label" for="delivery-{{ $item->id }}"></label>
        </div>
    </div>
    <div class="w-20 text-center">
        @if($edit)
            <button class="btn btn-outline-danger ml-6 product-remove" data-num = "{{ $i }}"
                    data-id="{{ $item->id }}" type="button" onclick="document.getElementById('form-remove-item-{{ $item->id }}').submit()">X</button>
            <form id="form-remove-item-{{ $item->id }}" method="post" action="{{ route('admin.sales.order.del-item', $item) }}">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>
