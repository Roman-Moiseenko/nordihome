<h2 class=" mt-3 font-medium">Контакты клиенты</h2>

<div class="box p-3 flex flex-row items-center lg:items-start mt-4">
    <div class="truncate sm:whitespace-normal flex items-center my-auto">
        <x-base.lucide icon="user" class="w-4 h-4"/>&nbsp;
        <a href="{{ route('admin.users.show', $order->user) }}">{{ $order->userFullName() }}</a>
    </div>
    <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
        <x-base.lucide icon="mail" class="w-4 h-4"/>&nbsp;<a
            href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a>
    </div>
    <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
        <x-base.lucide icon="phone" class="w-4 h-4"/>&nbsp;{{ $order->user->phone }}
    </div>
    <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
        <x-base.lucide icon="map" class="w-4 h-4"/>&nbsp;{{ $order->user->htmlDelivery() }}&nbsp;
        <button class="btn btn-warning-soft btn-sm ml-1">
            <x-base.lucide icon="pencil" class="w-4 h-4"/>
        </button>
    </div>
    <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
        <x-base.lucide icon="coins" class="w-4 h-4"/>&nbsp;{{ $order->user->htmlPayment() }}&nbsp;
        <button class="btn btn-warning-soft btn-sm ml-1">
            <x-base.lucide icon="pencil" class="w-4 h-4"/>
        </button>
    </div>
</div>

<h2 class=" mt-3 font-medium">Информация о заказе</h2>
<div class="box p-3 flex flex-col items-center lg:items-start mt-2">

    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="contact"
                       class="w-4 h-4"/>&nbsp;{{ $order->getManager()->fullname->getFullName() }} -
        менеджер
    </div>
    <div class="flex mt-3">
        <div class="">
            <span>Общий вес груза </span><span class="font-medium" id="weight">{{ $order->getWeight() }} кг</span>
        </div>
        <div class="ml-3">
            <span>Общий объем груза </span><span class="font-medium" id="volume">{{ $order->getVolume() }} м3</span>
        </div>
    </div>
    <div class="form-control mt-4">
        <label class="form-check-label" for="order-comment">Комментарий</label>
        <input id="order-comment" class="form-control update-data-ajax" type="text" name="comment"
               value="{{ $order->comment }}"
               data-route="{{ route('admin.sales.order.update-comment', $order) }}"
        >
    </div>
</div>

<h2 class=" mt-3 font-medium">Действия</h2>
<div class="box flex p-3 lg:justify-start buttons-block items-start">
    <x-base.popover class="inline-block w-100" placement="bottom-start">
        <x-base.popover.button as="x-base.button" variant="warning" class="w-100">Установить резерв
            <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
        </x-base.popover.button>
        <x-base.popover.panel>
            <form action="{{ route('admin.sales.order.set-reserve', $order) }}" METHOD="POST">
                @csrf
                <div class="p-2">
                    <x-base.form-input id="reserve-date" name="reserve-date" class="flex-1 mt-2"
                                       type="date"
                                       value="{{ $order->getReserveTo()->format('Y-m-d') }}"
                                       placeholder="Резерв"/>
                    <x-base.form-input name="reserve-time" class="flex-1 mt-2" type="time"
                                       value="{{ $order->getReserveTo()->format('H:i') }}"
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
    <button class="btn btn-success ml-2" type="button"
            onclick="document.getElementById('form-set-awaiting').submit();">На оплату
    </button>
    <form id="form-set-awaiting" method="post" action="{{ route('admin.sales.order.set-awaiting', $order) }}">
        @csrf
    </form>
    <x-base.popover class="inline-block mt-auto w-100 ml-2" placement="bottom-start">
        <x-base.popover.button as="x-base.button" variant="secondary" class="w-100">Отменить
            <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
        </x-base.popover.button>
        <x-base.popover.panel>
            <form action="{{ route('admin.sales.order.canceled', $order) }}" METHOD="POST">
                @csrf
                <div class="p-2">
                    <x-base.form-input name="comment" class="flex-1 mt-2" type="text" value=""
                                       placeholder="Комментарий"/>

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
</div>


