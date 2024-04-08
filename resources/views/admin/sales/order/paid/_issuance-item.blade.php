<div class="box flex items-center p-2">
    <div class="w-20 text-center">{{ $i + 1 }}</div>
    <div class="w-1/4">
        <div>{{ $item->product->name }}</div>
        <div>{{ $item->product->dimensions->weight() }} кг | {{ $item->product->dimensions->volume() }} м3</div>
    </div>
    <div class="w-32 text-center px-1">{{ price($item->sell_cost) }}</div>
    <div class="w-20 px-1 text-center">
        <input id="item-quantity-{{ $item->id }}" type="number" class="form-control text-center update-data-ajax"
               value="{{ $item->getRemains() }}" aria-describedby="input-quantity"
               min="1" max="{{ $item->getRemains() }}">
    </div>
    <div class="w-40 text-center">
        @foreach($item->product->getStorages() as $storage)
            <div class="{{ ($item->getRemains() > $storage->getQuantity($item->product)) ? 'text-danger' : '' }}">
                {{ $storage->getQuantity($item->product) . ' (' . $storage->name . ')' }}
            </div>
        @endforeach
    </div>
    <div class="w-20 text-center">
        <div class="form-check form-switch justify-center mt-3">
            <input id="item-{{ $item->id }}" class="form-check-input update-data-ajax"
                   data-input="item-quantity-{{ $item->id }}" type="checkbox" name="items" value="{{ $item->id }}" checked>
            <label class="form-check-label" for="item-{{ $item->id }}"></label>
        </div>
    </div>
</div>

