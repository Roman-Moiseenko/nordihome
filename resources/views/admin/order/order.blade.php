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
            <div class="box p-3 mt-3 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-3">
                    <div class="flex items-center border-b border-slate-200/60 pb-3 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Информация
                    </div>
                    <div class="mt-2 grid grid-cols-12 gap-x-6">
                        <div class="col-span-12 lg:col-span-6">
                            <livewire:admin.order.user-info :order="$order" />
                        </div>
                        <div class="col-span-12 lg:col-span-6">
                            <livewire:admin.order.manager.info :order="$order" />
                        </div>
                        <div class="col-span-12 lg:col-span-12">
                            <div class="box flex p-3 lg:justify-start buttons-block items-start">
                                @yield('actions')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @yield('showcontent')
        </div>
    </div>
@endsection
