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
    <div class="w-20 text-center">Скидка</div>
    <div class="w-20 text-center">Кол-во</div>
    <div class="w-20 text-center">Наличие</div>
    <div class="w-20 text-center">Сборка</div>
    <div class="w-40 text-center">Комментарий</div>
    <div class="w-20 text-center">Х</div>
</div>
@foreach($order->getInStock() as $i => $item)
    <livewire:admin.sales.order.item-order :item="$item" :i="$i" :edit="$order->isManager()" />

    @include('admin.sales.order.manager._product-item', ['i' => $i, 'item' => $item, 'edit' => $order->isManager()])
@endforeach

<h2 class=" mt-3 font-medium">Товар на заказ</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар/Габариты</div>
    <div class="w-32 text-center">Базовая/ Продажа</div>
    <div class="w-20 text-center">Скидка</div>
    <div class="w-20 text-center">Кол-во</div>
    <div class="w-20 text-center">Наличие</div>
    <div class="w-20 text-center">Сборка</div>
    <div class="w-20 text-center">Х</div>
</div>

@foreach($order->getPreOrder() as $i => $item)
    <livewire:admin.sales.order.item-order :item="$item" :i="$i" :edit="$order->isManager()" />
    @include('admin.sales.order.manager._product-item', ['i' => $i, 'item' => $item, 'edit' => $order->isManager()])
@endforeach

<livewire:admin.sales.order.amount-order :order="$order"/>

<h2 class=" mt-3 font-medium">Скидки</h2>
<div class="box mt-3 flex items-center p-2">
    <div class="flex items-center">
        <label for="coupon_code" class="mr-3">Купон на скидку: </label>
        <input id="coupon_code" type="text" class="w-20 form-control text-center update-data-ajax"
               value="{{ is_null($order->coupon_id) ? '' : $order->coupon->code }}" aria-describedby=""
               min="0"
               data-route="{{ route('admin.sales.order.set-coupon', $order) }}"
        >
        <div class="input-group">
            <input id="coupon" type="number" class="w-32 ml-2 form-control text-right"
                   value="{{ $order->coupon_amount }}" aria-describedby="discount-order"
                   readonly
            >
            <div id="discount-order" class="input-group-text">₽</div>
        </div>
    </div>
    <div class="flex items-center ml-3">
        <label for="discount_order" class="mr-3">Скидка вручную: </label>
        <div class="input-group">
            <input id="manual" type="number" class="w-32 form-control text-right update-data-ajax"
                   value="{{ $order->manual }}" aria-describedby="discount-order"
                   min="0" data-route="{{ route('admin.sales.order.discount', $order) }}"
            >
            <div id="discount-order" class="input-group-text">₽</div>
        </div>

        <div class="input-group ml-1">
            <input id="manual_percent" type="text" class="form-control  w-20 text-right update-data-ajax"
                   value="{{ ($order->getBaseAmountNotDiscount() == 0) ? 0 : number_format($order->manual / $order->getBaseAmountNotDiscount() * 100, 2, '.') }}"
                   aria-describedby="discount-percent"
                   min="0" max="50" data-route="{{ route('admin.sales.order.discount-percent', $order) }}"
            >
            <div id="discount-percent" class="input-group-text">%</div>
        </div>
    </div>
    <div class="flex items-center ml-3">
        Скидка: <label id="discount_name" for="discount_order" class="mr-3 ml-1">{{ $order->getDiscountName() }}</label>
        <div class="input-group">
            <input id="discount_order" type="number" class="w-32 ml-2 form-control text-right"
                   value="{{ $order->discount_amount }}" aria-describedby="discount-order"
                   readonly
            >
            <div id="discount-order" class="input-group-text">₽</div>
        </div>
    </div>
</div>
<h2 class=" mt-3 font-medium">Итого по товарам</h2>
<div class="box mt-3 flex items-center p-2">
    <div class="flex items-center">
        <label class="mr-3">Базовая сумма товаров</label>
        <div class="input-group">
            <input id="base_amount" type="number" class="w-32 ml-2 form-control text-right"
                   value="{{ $order->getBaseAmount() }}" aria-describedby="discount-order" readonly>
            <div id="discount-order" class="input-group-text">₽</div>
        </div>
    </div>

    <div class="flex items-center ml-3">
        <label class="mr-3">Сумма товаров</label>
        <div class="input-group">
            <input id="sell_amount" type="number" class="w-32 ml-2 form-control text-right"
                   value="{{ $order->getSellAmount() }}" aria-describedby="discount-order" readonly>
            <div id="discount-order" class="input-group-text">₽</div>
        </div>
    </div>
    <div class="flex items-center ml-3">
        <label class="mr-3">Скидка по товарам</label>
        <div class="input-group">
            <input id="discount_products" type="number" class="w-32 ml-2 form-control text-right"
                   value="{{ $order->getBaseAmount() - $order->getSellAmount() }}" aria-describedby="discount-order" readonly>
            <div id="discount-order" class="input-group-text">₽</div>
        </div>
    </div>
</div>
<h2 class=" mt-3 font-medium">Итого по заказу</h2>
<div class="box mt-3 flex items-center p-2">
    <div class="flex items-center">
        <label class="mr-3">Сумма за услуги</label>
        <div class="input-group">
            <input id="additions_amount" type="number" class="w-32 ml-2 form-control text-right"
                   value="{{ $order->getAdditionsAmount() }}" aria-describedby="discount-order" readonly>
            <div id="discount-order" class="input-group-text">₽</div>
        </div>
    </div>


    <div class="flex items-center ml-3">
        <label  class="mr-3">Скидка по заказу</label>
        <div class="input-group">
            <input id="all_discount_order" type="number" class="w-32 ml-2 form-control text-right"
                   value="{{ $order->getCoupon() + $order->getDiscountOrder() }}" aria-describedby="discount-order" readonly>
            <div id="discount-order" class="input-group-text">₽</div>
        </div>
    </div>

    <div class="flex items-center ml-3">
        <label class="mr-3">Всего к оплате</label>
        <div class="input-group">
            <input id="total_amount" type="number" class="w-32 ml-2 form-control text-right"
                   value="{{ $order->getTotalAmount() }}" aria-describedby="discount-order" readonly>
            <div id="discount-order" class="input-group-text">₽</div>
        </div>
    </div>
</div>
