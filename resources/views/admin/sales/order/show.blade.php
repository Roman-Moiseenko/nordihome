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
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
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
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Контакты клиенты</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="user" class="w-4 h-4"/>&nbsp;{{ $order->user->delivery->fullname->getFullName() }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="mail" class="w-4 h-4"/>&nbsp;<a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a>
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="phone" class="w-4 h-4"/>&nbsp;{{ $order->user->phone }}
                    </div>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Действия</div>
                <div class="flex flex-col items-center justify-center lg:justify-start mt-2">
                    ****
                    от текущего статуса разные действия
                    <button class="btn btn-primary">Назначить ответственного</button>
                    <button class="btn btn-primary">Установить резерв</button>
                    <button class="btn btn-primary">На оплату</button>
                    <button class="btn btn-primary">На сборку</button>
                    <button class="btn btn-primary">Изменить кол-во товара</button>
                    <button class="btn btn-primary">Изменить текущий статус</button>
                    <button class="btn btn-light">Отменить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
        <x-base.table class="table table-hover">
            <x-base.table.thead class="table-dark">
                <x-base.table.tr>
                    <x-base.table.th class="whitespace-nowrap">IMG</x-base.table.th>
                    <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ЦЕНА БАЗОВАЯ</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ЦЕНА СО СКИДКОЙ</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">АКЦИЯ/СКИДКА</x-base.table.th>
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

    <div class="font-medium text-xl text-danger mt-6">
        В разработке.<br>
        <br>
        Действия: <br>
        Назначить менеджера - Только админ, или автоматически, кто работает, по алгоритму <br>
        //до назначения, можно только отменить.<br>
        //после назначения заказ доступен в окне менеджера ... подумать по Админке работе с Заказами<br>

        Подвердить - смена статуса, отправка платежных документов клиенту, установка резерва (сколько).<br>
        Установить вручную время резерва<br>
        Отменить - <br>
        Если оплачен<br>
        Сформировать заявку на сборку (? автоматически)<br>

        Delivery<br>

        <br>
        Таблица изменений заказа: order_id, старая сумма, новая сумма, кто менял (сотрудник)<br>
        Таблица изменений позиций: order_item_id, старое кол-во, новое кол-во, отменен? product_id, цена
        продажи.....<br>
        В письме клиенту, отдельная таблица с отмененными товарами и кол-вом<br>
        <br>
        Список товаров в заказе (Название, ссылка, артикул, цена/скидка, кол-во ... действия (Уменьшить кол-во, отменить
        - причина (нет в наличии))

    </div>
@endsection
