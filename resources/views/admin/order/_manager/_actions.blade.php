<h2 class=" mt-3 font-medium">Действия</h2>
<div class="box flex p-3 lg:justify-start buttons-block items-start">
    <x-base.popover class="inline-block" placement="bottom-start">
        <x-base.popover.button as="x-base.button" variant="warning" class="">Установить резерв
            <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
        </x-base.popover.button>
        <x-base.popover.panel>
            <form action="{{ route('admin.order.set-reserve', $order) }}" METHOD="POST">
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
    <form id="form-set-awaiting" method="post" action="{{ route('admin.order.set-awaiting', $order) }}">
        @csrf
    </form>
    <x-base.popover class="inline-block mt-auto ml-2" placement="bottom-start">
        <x-base.popover.button as="x-base.button" variant="secondary" class="">Отменить
            <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
        </x-base.popover.button>
        <x-base.popover.panel>
            <form action="{{ route('admin.order.canceled', $order) }}" METHOD="POST">
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

    <button class="btn btn-success-soft ml-auto" type="button"
            onclick="document.getElementById('form-order-invoice').submit();"
    >Предварительный счет</button>
    <form id="form-order-invoice" method="post" action="{{ route('admin.order.invoice', $order) }}">
        @csrf
    </form>
</div>
