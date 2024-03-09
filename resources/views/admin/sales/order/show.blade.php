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
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 pt-5 lg:pt-0">

                <div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Информация о заказе</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="history" class="w-4 h-4"/>&nbsp;{{ $order->statusHtml() }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="badge-russian-ruble" class="w-4 h-4"/>&nbsp;{{ price($order->total) }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="package" class="w-4 h-4"/>&nbsp;{{ count($order->items) }} товаров
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="boxes" class="w-4 h-4"/>&nbsp;{{ $order->getQuantity() }} шт.
                    </div>

                    @if(!$order->isStatus(\App\Modules\Order\Entity\Order\OrderStatus::PAID) && !$order->isCanceled())
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <x-base.lucide icon="baggage-claim" class="w-4 h-4"/>&nbsp;{{ $order->getReserveTo() }}
                        </div>
                    @endif
                    <div
                        class="truncate sm:whitespace-normal flex items-center border-b w-100 border-slate-200/60 pb-2 mb-2"
                        style="width: 100%;"></div>
                    @if(!empty($order->getManager()))
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <x-base.lucide icon="contact"
                                           class="w-4 h-4"/>&nbsp;{{ $order->getManager()->fullname->getFullName() }} -
                            менеджер
                        </div>
                    @endif
                    @if(!empty($order->getLogger()))
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <x-base.lucide icon="contact"
                                           class="w-4 h-4"/>&nbsp;{{ $order->getLogger()->fullname->getFullName() }} -
                            логист
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-slate-200/60 lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Доставка</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="truck" class="w-4 h-4"/>&nbsp;{{ $order->delivery->typeHTML() }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="map-pin" class="w-4 h-4"/>&nbsp;{{ $order->delivery->address }}
                    </div>

                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="anvil" class="w-4 h-4"/>&nbsp;{{ $order->weight() }} кг
                    </div>

                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="box" class="w-4 h-4"/>&nbsp;{{ $order->volume() }} м3
                    </div>
                    <div
                        class="truncate sm:whitespace-normal flex items-center border-b w-100 border-slate-200/60 pb-2 mb-2"
                        style="width: 100%;"></div>
                    @if($order->isPoint())
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <x-base.lucide icon="warehouse" class="w-4 h-4"/>&nbsp;{{ 'Выдача/сборка: ' . $order->delivery->point->name }}
                        </div>
                    @endif

                    @if($order->delivery->cost != 0)
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <x-base.lucide icon="badge-russian-ruble" class="w-4 h-4"/>&nbsp;Доставка&nbsp;{{ price($order->delivery->cost) }}
                        </div>
                    @endif
                    @if(!empty($order->movements))
                    <div class="truncate sm:whitespace-normal flex items-center flex-col">
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <x-base.lucide icon="truck" class="w-4 h-4"/>&nbsp;Перемещения со складов:
                        </div>
                        @foreach($order->movements as $i => $movement)
                        <div>
                            {{ '#' . (int)($i + 1) . ' ' . $movement->storageOut->name . ($movement->isCompleted() ? ': Исполнено' : ': В ожидании') }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Контакты клиенты</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="user"
                                       class="w-4 h-4"/>&nbsp;{{ $order->user->delivery->fullname->getFullName() }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="mail" class="w-4 h-4"/>&nbsp;<a
                            href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a>
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="phone" class="w-4 h-4"/>&nbsp;{{ $order->user->phone }}
                    </div>
                </div>
            </div>

            <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 pt-5 lg:pt-0">
                @include('admin.sales.order._actions')
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
    <div class="font-medium text-xl text-danger mt-6">
        В разработке.<br>
        <br>
        Подтвердить - смена статуса, отправка платежных документов клиенту.<br>
        Если оплачен<br>
        Сформировать заявку на сборку (? автоматически)<br>
        Delivery<br>
        <br>

        В письме клиенту, отдельная таблица с отмененными товарами и кол-вом<br>
        <br>
        Список товаров в заказе (Название, ссылка, артикул, цена/скидка, кол-во ... действия (Уменьшить кол-во, отменить
        - причина (нет в наличии))

    </div>
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
