@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $order->htmlDate() . ' ' .$order->htmlNum() }} {{ $order->statusHtml() }}
            </h1>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 pb-20">
        <!-- ORDER -->
        <div class="col-span-11 lg:col-span-9">
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Информация
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.manager._info')
                    </div>
                </div>
            </div>
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Товары
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.manager._products')
                    </div>
                </div>
            </div>
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Услуги
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.manager._additions')
                    </div>
                </div>
            </div>

        </div>
        <div class="col-span-3 lg:block">
            <div class="fixed fixed-top pt-5">

                @include('admin.sales.order.manager._actions')
                <div class="relative mt-2 rounded-md border border-info bg-info/20 p-5">
                    <h2 class="text-lg font-medium">Общая информация</h2>
                    <div class="mt-2 leading-relaxed text-slate-600">
                        <div class="">
                            <span>Базовая сумма за товар </span>
                            <span class="font-medium" id="base_amount">{{ price($order->getBaseAmount()) }}</span>
                        </div>
                        <div class="">
                            <span>Скидка по товарам </span>
                            <span class="font-medium" id="discount_products">{{ price($order->getDiscountProducts()) }}</span>
                        </div>
                        <div class="">
                            <span>Скидка по заказу </span>
                            <span class="font-medium" id="discount_order">{{ price($order->getDiscountOrder()) }}</span>
                        </div>
                        <div class="">
                            <span>Скидка по купону </span>
                            <span class="font-medium" id="coupon">{{ price($order->getCoupon()) }}</span>
                        </div>
                        <div class="">
                            <span>Скидка ручная </span>
                            <span class="font-medium" id="manual">{{ price($order->getManual()) }}</span>
                        </div>

                        <div class="">
                            <span>Сумма за услуги </span>
                            <span class="font-medium" id="additions_amount">{{ price($order->getAdditionsAmount()) }}</span>
                        </div>
                        <div class="">
                            <span>Сборка мебели </span>
                            <span class="font-medium" id="assemblage_amount">{{ price($order->getAssemblageAmount()) }}</span>
                        </div>

                        <div class="mt-1">
                            <span>Итого за товары </span>
                            <span class="font-medium" id="sell_amount">{{ price($order->getSellAmount()) }}</span>
                        </div>
                        <div class="mt-2 text-base">
                            <span>К оплате всего </span>
                            <span class="font-medium" id="total_amount">{{ price($order->getTotalAmount()) }}</span>
                        </div>
                    </div>

                    <div class="text-sm mt-4">
                        <div class="">
                            <span>Общий вес груза </span><span class="font-medium" id="weight">{{ $order->getWeight() }} кг</span>
                        </div>
                        <div class="mt-2">
                            <span>Общий объем груза </span><span class="font-medium" id="volume">{{ $order->getVolume() }} м3</span>
                        </div>
                    </div>
                    <div class="text-xs mt-3">
                        Доставка рассчитывается в ручном режиме.
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        let inputUpdateData = document.querySelectorAll('.update-data-ajax');
        Array.from(inputUpdateData).forEach(function (input) {
            input.addEventListener('change', function () {
                let value = input.value;
                let route = input.getAttribute('data-route');
                let field = 'value';
                input.disabled = true;
                setAjax(route, field, value, input);
            });
        });

        function setAjax(route, field, value, element) {
            let _params = '_token=' + '{{ csrf_token() }}' + '&' + field + '=' + value;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(request.responseText);
                    element.disabled = false;
                    if(data.notupdate !== undefined) return;
                    if (data.error !== undefined) {
                        //Notification
                        window.notification('Ошибка',data.error ,'danger');
                        return;
                    }
                    console.log(data);
                    let order = data.order;
                    let items = data.items;
                    for (let key in order) {
                        if (document.getElementById(key) !== null) {
                            document.getElementById(key).innerText = order[key];
                            document.getElementById(key).value = order[key];
                        }
                    }

                    for (let i = 0; i < items.length; ++i) {
                        if (document.getElementById('sell_cost-' + items[i].id) !== null) document.getElementById('sell_cost-' + items[i].id).value = items[i].sell_cost;
                        if (document.getElementById('percent-' + items[i].id) !== null) document.getElementById('percent-' + items[i].id).value = items[i].percent;
                        console.log(items[i]);
                    }
                }
            };
        }
    </script>
@endsection
