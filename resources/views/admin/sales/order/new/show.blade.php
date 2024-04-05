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
                        Товары
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.new._products')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-3 lg:block">
            <div class="fixed fixed-top pt-5">
                @include('admin.sales.order.awaiting._actions')
            </div>
        </div>
    </div>
@endsection
