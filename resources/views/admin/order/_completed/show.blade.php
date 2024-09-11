@extends('admin.order.order')

@section('actions')
    @include('admin.order._completed._actions', ['order' => $order])
@endsection

@section('showcontent')
    <div class=" box p-3 mt-3 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-3">
            <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Выданные Товары и Услуги
            </div>
            <div class="mt-5">
                @include('admin.order._completed._products')
            </div>
            @if($order->additions()->count() > 0)
            <div class="mt-5">
                @include('admin.order._completed._additions')
            </div>
            @endif
        </div>
    </div>
    @if(!is_null($order->refund))
    <div class="box p-3 mt-3 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-3">
            <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Возврат товаров и услуг
            </div>
            <div class="mt-5">
                <a href="{{ route('admin.order.refund.show', $order->refund) }}" class="font-medium text-success">
                    Возврат от {{ $order->refund->htmlDate() }} на сумму {{ price($order->refund->amount) }}
                </a>
            </div>
        </div>
    </div>
    @endif
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
            <div class="mt-5">
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
            <div class="mt-5">
                @include('admin.order._paid._payments')
            </div>
        </div>
    </div>
    @endif

@endsection
