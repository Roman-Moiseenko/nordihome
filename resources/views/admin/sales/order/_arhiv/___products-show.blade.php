
@if($order->isManager())
    <div class="mx-3 flex w-full mb-5">
        <input id="route-search" type="hidden" value="{{ route('admin.sales.order.get-to-order') }}">
        <x-searchProduct route="{{ route('admin.sales.order.search') }}"
                         input-data="order-product" hidden-id="product_id" class="w-1/3"/>
        {{ \App\Forms\Input::create('quantity', ['placeholder' => 'Кол-во', 'value' => 1, 'class' => 'ml-2 w-20'])->type('number')->show() }}
        <x-base.button id="add-product" type="button" variant="primary" class="ml-3">Добавить товар в документ
        </x-base.button>
    </div>
@endif
<h2 class=" mt-3 font-medium">Товар в наличии</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар</div>
    <div class="w-32 text-center">Цена</div>
    <div class="w-32 text-center">Кол-во</div>
    <div class="w-40 text-center">Габариты</div>
    <div class="w-40 text-center">Наличие</div>
    <div class="w-20 text-center">-</div>
</div>
@foreach($order->getInStock() as $i => $item)
    <div class="box flex items-center  p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-1/4 text-center">{{ $item->product->name }}</div>
        <div class="w-32 text-center">
            @if($item->base_cost != $item->sell_cost)
                <s>{{ price($item->base_cost) }}</s><br> {{ price($item->sell_cost) }}
            @else
                {{ price($item->base_cost) }}
            @endif
        </div>
        <div class="w-32 input-group">
            <input id="" type="number" class="form-control text-right quantity-input"
                   value="{{ $item->quantity }}" aria-describedby="input-quantity"
                   min="0" data-array="free" data-num="0" @if(!$order->isManager()) readonly @endif>
            <div id="input-quantity" class="input-group-text">шт.</div>
        </div>
        <div class="w-40 text-center">
            {{ $item->product->dimensions->weight() }} кг<br> {{ $item->product->dimensions->volume() }} м3
        </div>
        <div class="w-40 text-center">
            @foreach($item->product->getStorages() as $storage)
                {{ $storage->getQuantity($item->product) . '(' . $storage->name . ')' }}<br>
            @endforeach
        </div>
        <div class="w-20 text-center">
            @if($order->isManager())
                <button class="btn btn-outline-danger ml-6 product-remove" data-num = "{{ $i }}"
                        data-id="{{ $item->product->id }}" data-array="free" type="button">X</button>
            @endif
        </div>
    </div>
@endforeach

<h2 class=" mt-3 font-medium">Товар на заказ</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар</div>
    <div class="w-32 text-center">Цена</div>
    <div class="w-32 text-center">Кол-во</div>
    <div class="w-20 text-center">-</div>
</div>

@foreach($order->getPreOrder() as $i => $item)

    <div class="box flex items-center  p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-1/4 text-center">{{ $item->product->name }}</div>
        <div class="w-32 text-center">{{ price($item->base_cost) }}</div>
        <div class="w-32 input-group">
            <input id="" type="number" class="form-control text-right quantity-input"
                   value="{{ $item->quantity }}" aria-describedby="input-quantity"
                   min="0" data-array="preorder" data-num="0" @if(!$order->isManager()) readonly @endif>
            <div id="input-quantity" class="input-group-text">шт.</div>
        </div>
        <div class="w-20 text-center">
            @if($order->isManager())
                <button class="btn btn-outline-danger ml-6 product-remove" data-num = "{{ $i }}"
                data-id="{{ $item->product->id }}" data-array="preorder" type="button">X</button>
            @endif
        </div>
    </div>
@endforeach
