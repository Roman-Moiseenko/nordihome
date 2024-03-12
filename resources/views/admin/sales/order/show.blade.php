@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $order->htmlDate() . ' ' .$order->htmlNum() }}
            </h1>
        </div>
    </div>
    <div class="intro-y box px-5 pt-5 mt-5">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60  pb-5 -mx-5">
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 pt-5 lg:pt-0">
                @include('admin.sales.order._info-order')
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-slate-200/60 lg:border-t-0 pt-5 lg:pt-0">
                @include('admin.sales.order._info-delivery')
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 border-t lg:border-t-0 pt-5 lg:pt-0">
                @include('admin.sales.order._info-user')
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 pt-5 lg:pt-0">
                @include('admin.sales.order._info-actions')
            </div>
        </div>
    </div>

    <div class="box col-span-12 overflow-auto lg:overflow-visible p-4 mt-4">
        <x-base.table class="table table-hover">
            <x-base.table.thead class="table-dark">
                <x-base.table.tr>
                    <x-base.table.th class="whitespace-nowrap">IMG</x-base.table.th>
                    <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ЦЕНА БАЗОВАЯ</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ЦЕНА СО СКИДКОЙ</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ГАБАРИТЫ</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">НА СКЛАДАХ</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                </x-base.table.tr>
            </x-base.table.thead>
            <x-base.table.tbody>
                @foreach($order->items as $item)
                    @include('admin.sales.order._item', ['item' => $item])
                @endforeach
            </x-base.table.tbody>
        </x-base.table>
    </div>

    @if($order->delivery->isRegion())
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4 mt-4">
            <h2 class="text-lg font-medium mr-auto">Упаковка</h2>
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ГАБАРИТЫ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">МАТЕРИАЛ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ВЕС</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЭФИЦИЕНТ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СТОИМОСТЬ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($order->items as $item)

                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    @endif

    <div class="box col-span-12 overflow-auto lg:overflow-visible p-4 mt-4">
            <h2 class="text-lg font-medium mr-auto">Платежи</h2>
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ДАТА СОЗДАНИЯ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">СУММА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СПОСОБ ПЛАТЕЖА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">НАЗНАЧЕНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОММЕНТАРИЙ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ОПЛАТА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($order->payments as $payment)
                        @include('admin.sales.order._payment', ['payment' => $payment])
                    @endforeach
                    @if($order->isManager())
                        @include('admin.sales.order._payment-new', ['order' => $order])
                    @endif
                </x-base.table.tbody>
            </x-base.table>
        </div>

    <div class="font-medium text-xl text-danger mt-6">
        В разработке.<br>
        <br>
        Подтвердить - смена статуса, отправка платежных документов клиенту.<br>
        Если оплачен<br>
        Сформировать заявку на сборку (? автоматически)<br>

    </div>

    @if($order->isManager())
        {{ \App\Forms\ModalDelete::create('Вы уверены?',
            'Вы действительно хотите удалить платеж?<br>Этот процесс не может быть отменен.', 'id-delete-payment')->show() }}
    @endif

    <script>
        let changeButton = document.getElementById('change-count-item');
        let inputItem = document.querySelectorAll('input[name=new-quantity]');
        changeButton.addEventListener('click', function () {
            if (changeButton.getAttribute('for-change') !== '1') {
                changeButton.setAttribute('for-change', '1');
                changeButton.textContent = 'Сохранить изменения';
                inputItem.forEach(function (element) {
                    element.setAttribute('type', 'number');
                });

            } else {
                changeButton.setAttribute('for-change', '0');
                changeButton.textContent = 'Изменить кол-во товара';
                //сохраняем через Ajax и перегружаем страницу
                let data = [];
                let route = changeButton.getAttribute('data-route');
                inputItem.forEach(function (element) {
                    data.push({
                        id: element.getAttribute('data-id'),
                        quantity: element.value
                    })
                    element.setAttribute('type', 'hidden');
                });
                setAjax(data, route)
            }
        });

        function setAjax(data, route) {
            let _params = '_token=' + '{{ csrf_token() }}' + '&items=' + JSON.stringify(data);
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let _data = JSON.parse(request.responseText);
                    //console.log(_data);
                    if (_data === true) {
                        location.reload();
                    } else {
                        console.log(_data);
                    }
                }
            };
        }
    </script>
@endsection
