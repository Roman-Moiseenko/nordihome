<div class="col-lg-2 col-md-4 col-6 p-3">
    <div class="order-item-block">
        <img src="{{ $item->product->photo->getThumbUrl('thumb') }}" title="{{ $item->product->name }}">
        @if($item->quantity > 1)
            <span class="order-item-container"><span class="fs-8 order-item-quantity">{{ $item->quantity }}шт.</span></span>
        @endif
    </div>
    <div class="fs-7 text-center" style="color: var(--bs-gray-600);">{{ price($item->cost) }}/шт.</div>
</div>
