<form method="post" action="{{ route('admin.sales.order.add-item', $order) }}">
    @csrf
    <div class="mx-3 flex w-full mb-5">
        <input id="route-search" type="hidden" value="{{ route('admin.sales.order.get-to-order') }}">
        <x-searchProduct route="{{ route('admin.sales.order.search') }}"
                         input-data="order-product" hidden-id="product_id" class="w-1/3"/>
        {{ \App\Forms\Input::create('quantity', ['placeholder' => 'Кол-во', 'value' => 1, 'class' => 'ml-2 w-20'])->type('number')->min_max(1, null)->show() }}
        <x-base.button id="add-product" type="submit" variant="primary" class="ml-3">Добавить товар в документ
        </x-base.button>
    </div>
</form>

<h2 class=" mt-3 font-medium">Товар в наличии</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар/Габариты</div>
    <div class="w-32 text-center">Базовая/ Продажа</div>
    <div class="w-20 text-center">Кол-во</div>
    <div class="w-40 text-center">Наличие/Резерв/Склад</div>
    <div class="w-20 text-center">Сборка</div>
    <div class="w-40 text-center">Комментарий</div>
    <div class="w-20 text-center">Х</div>
</div>
@foreach($order->getInStock() as $i => $item)
    @include('admin.sales.order.manager._product-item', ['i' => $i, 'item' => $item, 'edit' => $order->isManager()])
@endforeach

<h2 class=" mt-3 font-medium">Товар на заказ</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар/Габариты</div>
    <div class="w-32 text-center">Базовая/ Продажа</div>
    <div class="w-20 text-center">Кол-во</div>
    <div class="w-40 text-center">Наличие/Резерв/Склад</div>
    <div class="w-20 text-center">Сборка</div>
    <div class="w-20 text-center">Х</div>
</div>

@foreach($order->getPreOrder() as $i => $item)
    @include('admin.sales.order.manager._product-item', ['i' => $i, 'item' => $item, 'edit' => $order->isManager()])
@endforeach
