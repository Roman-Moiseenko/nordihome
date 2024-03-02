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
                    <div class="truncate sm:whitespace-normal flex items-center" >
                        <x-base.lucide icon="boxes" class="w-4 h-4"/>&nbsp;{{ $order->getQuantity() }} шт.
                    </div>
                    @if(!$order->isStatus(\App\Modules\Order\Entity\Order\OrderStatus::PAID))
                    <div class="truncate sm:whitespace-normal flex items-center border-b w-100 border-slate-200/60 pb-2 mb-2" style="width: 100%;">
                        <x-base.lucide icon="baggage-claim" class="w-4 h-4"/>&nbsp;{{ $order->getReserveTo() }}
                    </div>
                    @endif
                    @if(!empty($order->getManager()))
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <x-base.lucide icon="contact"
                                           class="w-4 h-4"/>&nbsp;{{ $order->getManager()->fullname->getFullName() }} - менеджер
                        </div>
                    @endif
                    @if(!empty($order->getLogger()))
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <x-base.lucide icon="contact"
                                           class="w-4 h-4"/>&nbsp;{{ $order->getLogger()->fullname->getFullName() }} - логист
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
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Действия</div>
                <div class="flex flex-col lg:justify-start mt-2">
                    @if($order->isNew())
                        <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
                            <x-base.popover.button as="x-base.button" variant="primary" class="w-100">Назначить
                                ответственного
                                <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                            </x-base.popover.button>
                            <x-base.popover.panel>
                                <form action="{{ route('admin.sales.order.set-manager', $order) }}" METHOD="POST">
                                    @csrf
                                    <div class="p-2">
                                        <x-base.tom-select id="select-staff" name="staff_id" class=""
                                                           data-placeholder="Выберите Менеджера">
                                            <option value="0"></option>
                                            @foreach($staffs as $staff)
                                                <option value="{{ $staff->id }}"
                                                >{{ $staff->fullname->getShortName() }}</option>
                                            @endforeach
                                        </x-base.tom-select>

                                        <div class="flex items-center mt-3">
                                            <x-base.button id="close-add-group" class="w-32 ml-auto"
                                                           data-tw-dismiss="dropdown" variant="secondary" type="button">
                                                Отмена
                                            </x-base.button>
                                            <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                                Сохранить
                                            </x-base.button>
                                        </div>
                                    </div>
                                </form>
                            </x-base.popover.panel>
                        </x-base.popover>
                        <button class="btn btn-secondary mt-2">Удалить</button>
                    @endif
                    @php
                    //TODO Открыть блокировку по id персонала
                    @endphp
                    @if($order->isManager()/* && $order->getManager()->id == $admin->id*/)
                        <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
                                <x-base.popover.button as="x-base.button" variant="warning" class="w-100">Установить резерв
                                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                                </x-base.popover.button>
                                <x-base.popover.panel>
                                    <form action="{{ route('admin.sales.order.set-reserve', $order) }}" METHOD="POST">
                                        @csrf
                                        <div class="p-2">
                                            <x-base.form-input id="reserve-date" name="reserve-date" class="flex-1 mt-2" type="date" value="{{ $order->getReserveTo()->format('Y-m-d') }}"
                                                               placeholder="Резерв"/>
                                            <x-base.form-input name="reserve-time" class="flex-1 mt-2" type="time" value="{{ $order->getReserveTo()->format('H:i') }}"
                                                               placeholder=""/>
                                            <div class="flex items-center mt-3">
                                                <x-base.button id="close-add-group" class="w-32 ml-auto"
                                                               data-tw-dismiss="dropdown" variant="secondary" type="button">
                                                    Отмена
                                                </x-base.button>
                                                <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                                    Сохранить
                                                </x-base.button>
                                            </div>
                                        </div>
                                    </form>
                                </x-base.popover.panel>
                            </x-base.popover>

                        <button id="change-count-item" class="btn btn-danger mt-2"
                                data-route="{{ route('admin.sales.order.set-quantity', $order) }}">Изменить кол-во товара</button>
                        <button class="btn btn-success mt-2">На оплату</button>
                        <button class="btn btn-secondary mt-2">Отменить</button>
                    @endif
                    @if($order->isAwaiting()/* && $order->getManager()->id == $admin->id*/)
                        <button class="btn btn-warning">Установить резерв</button>
                        <button class="btn btn-secondary mt-2">Отменить</button>
                    @endif
                    @if($order->isPaid()/* && $order->getManager()->id == $admin->id*/)
                        <button class="btn btn-primary">Изменить текущий статус</button>
                        <button class="btn btn-success mt-2">На сборку</button>
                    @endif
                    @if($order->isToDelivery())
                        <span>В службе сборки</span>
                    @endif
                    @if($order->isCanceled())
                        <span>Заказ отменен</span>
                    @endif
                    @if($order->isCompleted())
                        <span>Завершен</span>
                    @endif
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
        //после назначения заказ доступен в окне менеджера ... подумать по Админке работе с Заказами<br>

        Подвердить - смена статуса, отправка платежных документов клиенту, установка резерва (сколько).<br>

        Отменить - <br>
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
            //AJAX
            let _params = '_token=' + '{{ csrf_token() }}' + '&items=' + JSON.stringify(data);
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    if (request.responseText === true) window.location.reload();
                    console.log(request.responseText);
                } else {
                    //console.log(request.responseText);
                }
            };
        }
    </script>
@endsection
