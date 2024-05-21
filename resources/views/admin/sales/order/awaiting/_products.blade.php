<h2 class=" mt-3 font-medium">Товар в наличии</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-10 text-center">№ п/п</div>
    <div class="w-32 text-center">Артикул</div>
    <div class="w-1/4 text-center">Товар</div>
    <div class="w-32 text-center">Цена продажи</div>
    <div class="w-20 text-center">Кол-во</div>
    <div class="w-20 text-center">Сумма</div>
</div>
@foreach($order->items as $i => $item)
    <div class="box flex items-center p-2">
        <div class="w-10 text-center">{{ $i + 1 }}</div>
        <div class="w-32 text-center">{{ $item->product->code }}</div>
        <div class="w-1/4">{{ $item->product->name }} @if($item->preorder) <b>(предзаказ)</b> @endif</div>
        <div class="w-32 text-center px-1">{{ price($item->sell_cost) }}</div>
        <div class="w-20 px-1 text-center">{{ $item->quantity }}</div>
        <div class="w-32 text-center px-1">{{ price($item->sell_cost * $item->quantity) }}</div>
    </div>
@endforeach
<div class="box flex items-center font-semibold p-2">
    <div class="w-10 text-center"></div>
    <div class="w-32 text-center">Артикул</div>
    <div class="w-1/4 text-left">ИТОГО</div>
    <div class="w-32 text-center"></div>
    <div class="w-20 text-center"></div>
    <div class="w-20 text-center">{{ price($order->getSellAmount()) }}</div>
</div>
