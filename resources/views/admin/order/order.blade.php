@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $order->htmlDate() . ' ' .$order->htmlNum() }} {{ $order->statusHtml() }} <em>{{ $order->getType() }}</em>
            </h1>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 pb-20">
        <div class="col-span-12 lg:col-span-12">
            <!-- Информация о заказе -->
            <div class="box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Информация
                    </div>
                    <div class="mt-5">
                        <livewire:admin.order.user-info :order="$order" />
                        <livewire:admin.order.manager.info :order="$order" />
                        @yield('actions')
                    </div>
                </div>
            </div>
            @yield('showcontent')
        </div>
    </div>
@endsection
