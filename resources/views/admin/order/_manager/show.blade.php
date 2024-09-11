@extends('admin.order.order')

@section('actions')
    @include('admin.order._manager._actions', ['order' => $order])
@endsection

@section('showcontent')
    <div class="box p-3 mt-3 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-3">
            <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Товары
            </div>
            <div class="mt-3">
                <div class="mx-3 flex w-full mb-3">
                    @if($order->isParser())
                        <x-searchAddParser event="add-parser" quantity="1" width="100"/>
                    @else
                        <x-searchAddProduct event="add-product"
                                            quantity="1" parser="1" width="100"
                                            show-stock="1" published="1" preorder="1"
                                            caption="Добавить товар"/>
                    @endif
                        <x-createAddProduct event="add-product" />
                </div>
                <livewire:admin.order.manager.items :order="$order"/>
                <livewire:admin.order.manager.amount :order="$order"/>
            </div>
        </div>
    </div>
    <div class="box p-3 mt-3 block-menus-order">
        <div class="rounded-md border border-slate-200/60 p-3">
            <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium">
                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                Услуги
            </div>
            <div class="mt-5">
                <livewire:admin.order.manager.additions :order="$order" />
            </div>
        </div>
    </div>
@endsection
