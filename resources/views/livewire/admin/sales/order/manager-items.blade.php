<div>
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
        <livewire:admin.sales.order.manager-item :item="$item" :key="$item->id" :i="$i" :edit="$order->isManager()" />
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
        <livewire:admin.sales.order.manager-item :item="$item" :key="$item->id" :i="$i" :edit="$order->isManager()" />
    @endforeach
</div>
