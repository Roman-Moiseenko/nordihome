<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар/Габариты</div>
    <div class="w-32 text-center">Цена продажи</div>
    <div class="w-20 text-center">Кол-во</div>
</div>
@foreach($refund->items as $i => $item)
    <div class="box flex items-center p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-1/4 text-center">{{ $item->orderItem->product->name }}</div>
        <div class="w-32 text-center">{{ $item->orderItem->sell_cost }}</div>
        <div class="w-20 text-center">{{ $item->quantity }}</div>
    </div>
@endforeach
