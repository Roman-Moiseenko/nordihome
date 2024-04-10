@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $order->htmlDate() . ' ' .$order->htmlNum() }} - {{ $order->statusHtml() }}
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
                        @include('admin.sales.order.paid._info')
                    </div>
                </div>
            </div>
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Не выданные Товары и Услуги
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.paid._issuance')
                    </div>
                </div>
            </div>
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Распоряжения
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.paid._expenses')
                    </div>
                </div>
            </div>
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Платежи
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.paid._payments')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-3 lg:block">
            <div class="fixed fixed-top pt-5">
                @include('admin.sales.order.paid._actions')
                <div class="relative mt-2 rounded-md border border-info bg-info/20 p-5">
                    <h2 class="text-lg font-medium">Общая информация</h2>
                    <div class="mt-2 leading-relaxed text-slate-600">
                        <div class="">
                            <span>Сумма за заказ </span>
                            <span class="font-medium" id="base_amount">{{ price($order->getTotalAmount()) }}</span>
                        </div>
                        <div class="">
                            <span>Оплачено за заказ </span>
                            <span class="font-medium" id="discount_products">{{ price($order->getPaymentAmount()) }}</span>
                        </div>
                        <div class="">
                            <span>Остаток оплаты за заказ </span>
                            <span class="font-medium" id="discount_order">{{ price($order->getTotalAmount() - $order->getPaymentAmount()) }}</span>
                        </div>
                        <div class="">
                            <span>Распоряжения на сумму </span>
                            <span class="font-medium" id="coupon">{{ price($order->getExpenseAmount()) }}</span>
                        </div>
                        <div class="">
                            <span>Выдано товаров </span>
                            <span class="font-medium" id="manual">{{ -1 }}</span>
                        </div>

                        <div class="">
                            <span>Осталось выдать </span>
                            <span class="font-medium" id="additions_amount">{{ -1 }}</span>
                        </div>

                        <div class="mt-1">
                            <span>Не оказанные услуги </span>
                            <span class="font-medium" id="sell_amount">{{ price(-1) }}</span>
                        </div>
                    </div>

                    <div class="text-xs mt-3">
                        Для теста:<br>
                        getBaseAmount() - {{ $order->getBaseAmount() }}<br>
                        getSellAmount() - {{ $order->getSellAmount() }}<br>
                        getDiscountProducts() - {{ $order->getDiscountProducts() }}<br>
                        getDiscountOrder() - {{ $order->getDiscountOrder() }}<br>
                        getCoupon() - {{ $order->getCoupon() }}<br>
                        getManual() - {{ $order->getManual() }}<br>
                        getAdditionsAmount() - {{ $order->getAdditionsAmount() }}<br>
                        getAssemblageAmount() - {{ $order->getAssemblageAmount() }}<br>
                        getPaymentAmount() - {{ $order->getPaymentAmount() }}<br>
                        getExpenseAmount() - {{ $order->getExpenseAmount() }}<br>

                        Доставка рассчитывается в ручном режиме.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
