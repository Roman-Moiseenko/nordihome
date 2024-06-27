@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $order->htmlDate() . ' ' .$order->htmlNum() }} {{ $order->statusHtml() }} <em>{{ $order->getType() }}</em>
            </h1>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 pb-20">
        <!-- ORDER -->
        <div class="col-span-12 lg:col-span-12">
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Информация
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.manager._info')
                    </div>
                </div>
            </div>
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Товары
                    </div>
                    <div class="mt-5">
                        @include('admin.sales.order.manager._products')
                    </div>
                </div>
            </div>
            <div class="intro-y box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Услуги
                    </div>
                    <div class="mt-5">
                        <livewire:admin.sales.order.manager-additions :order="$order" />
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        let inputUpdateData = document.querySelectorAll('.update-data-ajax');
        Array.from(inputUpdateData).forEach(function (input) {
            input.addEventListener('change', function () {
                let value = input.value;
                let route = input.getAttribute('data-route');
                let field = 'value';
                input.disabled = true;
                setAjax(route, field, value, input);
            });
        });

        function setAjax(route, field, value, element) {
            let _params = '_token=' + '{{ csrf_token() }}' + '&' + field + '=' + value;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(request.responseText);
                    element.disabled = false;
                    if(data.notupdate !== undefined) return;
                    if (data.error !== undefined) {
                        //Notification
                        window.notification('Ошибка',data.error ,'danger');
                        return;
                    }
                    console.log(data);
                    let order = data.order;
                    let items = data.items;
                    for (let key in order) {
                        if (document.getElementById(key) !== null) {
                            document.getElementById(key).innerText = order[key];
                            document.getElementById(key).value = order[key];
                        }
                    }

                    for (let i = 0; i < items.length; ++i) {
                        if (document.getElementById('sell_cost-' + items[i].id) !== null) document.getElementById('sell_cost-' + items[i].id).value = items[i].sell_cost;
                        if (document.getElementById('percent-' + items[i].id) !== null) document.getElementById('percent-' + items[i].id).value = items[i].percent;
                        console.log(items[i]);
                    }
                }
            };
        }
    </script>
@endsection
