@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $order->htmlDate() . ' ' .$order->htmlNum() }} - {{ $order->statusHtml() }}
            </h1>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 pb-20">
        <!-- ORDER -->
        <div class="col-span-12">
            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Информация
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.completed._info')
                    </div>
                </div>
            </div>
            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Выданные Товары и Услуги
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.completed._products')
                    </div>
                    @if($order->additions()->count() > 0)
                    <div class="mt-5">
                        @include('admin.sales.order.completed._additions')
                    </div>
                    @endif
                </div>
            </div>
            @if(!is_null($order->refund))
            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Возврат товаров и услуг
                    </div>
                    <div class="mt-5">
                        <a href="{{ route('admin.sales.refund.show', $order->refund) }}" class="font-medium text-success">
                            Возврат от {{ $order->refund->htmlDate() }} на сумму {{ price($order->refund->amount) }}
                        </a>
                    </div>
                </div>
            </div>
            @endif
            @if($order->expenses->count() > 0)
            <div class="box p-3 mt-3 block-menus-order">
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
            @endif
            @if($order->movements->count() > 0)
            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Перемещения
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.paid._movements')
                    </div>
                </div>
            </div>
            @endif
            @if($order->payments->count() > 0)
            <div class="box p-3 mt-3 block-menus-order">
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
            @endif
        </div>
    </div>
@endsection
