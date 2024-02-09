<div class="col-lg-2 col-sm-6 p-3">
    <div class="order-item-block">
        <img src="{{ $item['img'] }}" title="{{ $item['name'] }}">
        @if($item['quantity'] > 1)
            <span class="order-item-container"><span class="fs-8 order-item-quantity">{{ $item['quantity'] }}шт.</span></span>
        @endif
    </div>
    <div class="fs-7 text-center" style="color: var(--bs-gray-600);">{{ price($item['price']) }}/шт.</div>
</div>
