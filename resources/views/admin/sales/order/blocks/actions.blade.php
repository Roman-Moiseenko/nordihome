<div class="flex flex-col lg:justify-start mt-2 buttons-block items-start">
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
        @if(!$order->delivery->isStorage())
            <x-base.popover class="inline-block mt-auto w-100 mt-2" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="warning" class="w-100">Рассчитать
                    доставку
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form action="{{ route('admin.sales.order.set-delivery', $order) }}" METHOD="POST">
                        @csrf
                        <div class="p-2">
                            <x-base.form-input name="delivery-cost" class="flex-1 mt-2" type="number"
                                               value="{{ $order->delivery->cost }}"
                                               placeholder=""/>
                            <div class="flex items-center mt-3">
                                <x-base.button id="close-add-group" class="w-32 ml-auto"
                                               data-tw-dismiss="dropdown" variant="secondary"
                                               type="button">
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
        @endif

        @if(!$order->isPoint())
            <x-base.popover class="inline-block mt-auto w-100 mt-2" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="warning" class="w-100">Точка
                    выдачи/сборки
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form action="{{ route('admin.sales.order.set-moving', $order) }}" METHOD="POST">
                        @csrf
                        <div class="p-2">
                            <x-base.tom-select id="select-storage" name="storage" class=""
                                               data-placeholder="Выберите Склад">
                                <option value="0"></option>
                                @foreach($storages as $storage)
                                    <option value="{{ $storage->id }}"
                                    >{{ $storage->name }}</option>
                                @endforeach
                            </x-base.tom-select>

                            <div class="flex items-center mt-3">
                                <x-base.button id="close-add-group" class="w-32 ml-auto"
                                               data-tw-dismiss="dropdown" variant="secondary"
                                               type="button">
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
        @endif
        <button class="btn btn-success mt-2" type="button"
                onclick="document.getElementById('form-set-awaiting').submit();">На оплату
        </button>
        <form id="form-set-awaiting" method="post" action="{{ route('admin.sales.order.set-awaiting', $order) }}">
            @csrf
        </form>
        <x-base.popover class="inline-block mt-auto w-100 mt-2" placement="bottom-start">
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
    @endif
    @if($order->isAwaiting()/* && $order->getManager()->id == $admin->id*/)
            <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
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
            <x-base.popover class="inline-block mt-auto w-100 mt-2" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="primary" class="w-100">Оплачен
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form action="{{ route('admin.sales.order.paid-order', $order) }}" METHOD="POST">
                        @csrf
                        <div class="p-2">
                            <x-base.form-input name="document" class="flex-1 mt-2" type="text" value=""
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

            <x-base.popover class="inline-block mt-auto w-100 mt-2" placement="bottom-start">
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
    @endif
    @if($order->isPaid()/* && $order->getManager()->id == $admin->id*/)
            <x-base.popover class="inline-block mt-2 w-100" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="primary" class="w-100">Изменить статус
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form action="{{ route('admin.sales.order.set-status', $order) }}" METHOD="POST">
                        @csrf
                        <div class="p-2">
                            <x-base.tom-select id="select-status" name="status" class=""
                                               data-placeholder="Изменить текущий статус">
                                <option value="0"></option>
                                @foreach($order->getAvailableStatuses(\App\Modules\Order\Entity\Order\OrderStatus::ORDER_SERVICE) as $code => $name)
                                    <option value="{{ $code }}"
                                    >{{ $name }}</option>
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
        <x-base.popover class="inline-block mt-2 w-100" placement="bottom-start">
            <x-base.popover.button as="x-base.button" variant="success" class="w-100">На сборку
                <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
            </x-base.popover.button>
            <x-base.popover.panel>
                <form action="{{ route('admin.sales.order.set-logger', $order) }}" METHOD="POST">
                    @csrf
                    <div class="p-2">
                        <x-base.tom-select id="select-logger" name="logger_id" class=""
                                           data-placeholder="Выберите Сборщика">
                            <option value="0"></option>
                            @foreach($loggers as $logger)
                                <option value="{{ $logger->id }}"
                                >{{ $logger->fullname->getShortName() }}</option>
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
    @endif
    @if($order->isToDelivery())
            <x-base.popover class="inline-block mt-2 w-100" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="warning" class="w-100">Изменить статус
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form action="{{ route('admin.sales.order.set-status', $order) }}" METHOD="POST">
                        @csrf
                        <div class="p-2">
                            <x-base.tom-select id="select-status" name="status" class=""
                                               data-placeholder="Изменить текущий статус">
                                <option value="0"></option>
                                @foreach($order->getAvailableStatuses(\App\Modules\Order\Entity\Order\OrderStatus::CANCEL) as $code => $name)
                                    <option value="{{ $code }}"
                                    >{{ $name }}</option>
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
            <button class="btn btn-primary mt-2" type="button" onclick="document.getElementById('form-order-completed').submit();">Заказ Выполнен!</button>
        <form id="form-order-completed" method="post" action="{{ route('admin.sales.order.completed', $order) }}">
            @csrf
        </form>

            <x-base.popover class="inline-block mt-auto w-100 mt-2" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="danger" class="w-100">Возврат
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form action="{{ route('admin.sales.order.refund', $order) }}" METHOD="POST">
                        @csrf
                        <div class="p-2">
                            <x-base.form-input name="refund" class="flex-1 mt-2" type="text" value=""
                                               placeholder="Причина"/>

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

    @endif
    @if($order->isCanceled())
        <span>Заказ отменен</span>
    @endif
    @if($order->isCompleted())
        <span>Завершен</span>
    @endif
</div>
