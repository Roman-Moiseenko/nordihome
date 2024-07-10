@extends('admin.sales.order.order')

@section('actions')
    @include('admin.sales.order.paid._actions', ['order' => $order])
@endsection

@section('showcontent')

    <div class="box p-3 mt-3 block-menus-order">
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
@endsection
