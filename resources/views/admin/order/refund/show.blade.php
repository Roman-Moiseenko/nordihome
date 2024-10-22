@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-5">
            <h1 class="text-lg font-medium mr-auto">
                {{ 'Возврат от ' . $refund->htmlDate() . ' на Заказа ' . $refund->order->htmlNum() . '(' . $refund->order->htmlDate() . ')' }}
            </h1>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 pb-20">
        <!-- REFUND -->
        <div class="col-span-12">

            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Информация
                    </div>
                    <div class="mt-5">
                        @include('admin.order.refund._info')
                    </div>
                </div>
            </div>
            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Возврат товаров
                    </div>
                    <div class="mt-5">
                        @include('admin.order.refund._products')
                    </div>
                </div>
            </div>
            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Не оказанные услуги
                    </div>
                    <div class="mt-5">
                        @include('admin.order.refund._additions')
                    </div>
                </div>
            </div>

            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Возврат по платежам
                    </div>
                    <div class="mt-5">
                        @include('admin.order.refund._payments')
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
