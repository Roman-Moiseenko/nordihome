@extends('admin.order.order')

@section('actions')
    @include('admin.order._paid._actions', ['order' => $order])
@endsection

@section('showcontent')

    <div class="box p-3 mt-3 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-3">
            <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Не выданные Товары и Услуги
            </div>
            <div class="mt-5">
                @include('admin.order._paid._issuance')
            </div>
        </div>
    </div>
    @if($order->expenses->count() > 0)
    <div class="box p-3 mt-3 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-3">
            <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Распоряжения
            </div>
            <div class="mt-5">
                @include('admin.order._paid._expenses')
            </div>
        </div>
    </div>
    @endif
    @if($order->movements->count() > 0)
    <div class="box p-3 mt-3 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-3">
            <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Перемещения
            </div>
            <div class="mt-3">
                @include('admin.order._paid._movements')
            </div>
        </div>
    </div>
    @endif
    @if($order->payments->count() > 0)
    <div class="box p-3 mt-3 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-3">
            <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Платежи
            </div>
            <div class="mt-3">
                @include('admin.order._paid._payments')
            </div>
        </div>
    </div>
    @endif
@endsection
