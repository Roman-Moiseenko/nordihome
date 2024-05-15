<div class="box flex items-center p-2">
    <div class="w-20 text-center">{{ $i + 1 }}</div>
    <div class="w-1/4">
        <div>{{ $item->product->name }}</div>
        <div>{{ $item->product->dimensions->weight() }} кг | {{ $item->product->dimensions->volume() }} м3</div>
    </div>
    <div class="w-32 text-center px-1">
        <div>{{ price($item->base_cost) }}</div>

            <input id="sell_cost-{{ $item->id }}" type="number" class="form-control text-center"
                   value="{{ $item->sell_cost }}" aria-describedby="input-sell_cost"
                   readonly
            >

    </div>
    <div class="w-20 text-center px-1">
        <div>%</div>
        <input id="percent-{{ $item->id }}" type="text" class="form-control text-center"
               value="{{ number_format(($item->base_cost - $item->sell_cost) / $item->base_cost * 100, 2) }}" aria-describedby="input-sell_cost"
               readonly
        >
    </div>

    <div class="w-20 px-1 text-center">
        <div> - </div>

        <input id="quantity-{{ $item->id }}" type="number" class="form-control text-center"
               value="{{ $item->quantity }}" aria-describedby="input-quantity"
               readonly>
    </div>

    <div class="w-40 text-left">
        <div> - </div>
        <input id="comment-{{ $item->id }}" type="text" class="form-control "
               value="{{ $item->comment }}" readonly
        >
    </div>
</div>
