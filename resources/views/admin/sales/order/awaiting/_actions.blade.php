<div class="flex flex-row lg:justify-start buttons-block items-start">
    <button class="w-32 mb-2 btn btn-success-soft" type="button"
            onclick="document.getElementById('form-send-invoice').submit();">
        Отправить счет повторно
    </button>
    <form id="form-send-invoice" method="post" action="{{ route('admin.sales.order.send-invoice', $order) }}">
        @csrf
    </form>

    <x-base.popover class="inline-block ml-2" placement="bottom-start">
        <x-base.popover.button as="x-base.button" variant="warning" class="">Установить резерв
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

    <x-base.popover class="inline-block ml-2" placement="bottom-start">
        <x-base.popover.button as="x-base.button" variant="secondary" class="">Отменить
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


