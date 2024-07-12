@extends('admin.order.order')


@section('actions')
    @include('admin.order._new._actions')
@endsection

@section('showcontent')
    <div class="box p-5 mt-5 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-5">
            <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Товары
            </div>
            <div class="mt-5">
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
                        <div class="w-32">{{ $item->product->code }}</div>
                        <div class="w-1/4">{{ $item->product->name }} @if($item->preorder) <b>(предзаказ)</b> @endif</div>
                        <div class="w-32 text-center px-1">{{ price($item->sell_cost) }}</div>
                        <div class="w-20 px-1 text-center">{{ $item->quantity }}</div>
                        <div class="w-32 text-center px-1">{{ price($item->sell_cost * $item->quantity) }}</div>
                    </div>
                @endforeach
                <div class="box flex items-center font-semibold p-2">
                    <div class="w-10 text-center"></div>
                    <div class="w-32"></div>
                    <div class="w-1/4 text-left">ИТОГО</div>
                    <div class="w-32 text-center"></div>
                    <div class="w-20 text-center"></div>
                    <div class="w-20 text-center">{{ price($order->getSellAmount()) }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
