<livewire:admin.sales.order.user-info :order="$order" />

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

    @if(!is_null($order->getReserveTo()))
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
    @endif

    @if($order->getQuantity() > $order->getQuantityExpense())
        <x-base.popover class="inline-block mt-auto w-100 ml-3" placement="bottom-start">
            <x-base.popover.button as="x-base.button" variant="dark" class="w-100">Перемещение
                <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
            </x-base.popover.button>
            <x-base.popover.panel>
                <form action="{{ route('admin.sales.order.movement', $order) }}" METHOD="POST">
                    @csrf
                    <div class="p-2">

                        <x-base.form-label for="select-storage-out" class="mt-3">Хранилище Убытие</x-base.form-label>
                        <x-base.tom-select id="select-storage-out" name="storage_out" class="w-full" data-placeholder="Выберите хранилище">
                            <option value="0"></option>
                            @foreach($storages as $storage)
                                <option value="{{ $storage->id }}">
                                    {{ $storage->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>

                        <x-base.form-label for="select-storage-in" class="mt-3">Хранилище Прибытие</x-base.form-label>
                        <x-base.tom-select id="select-storage-in" name="storage_in" class="w-full" data-placeholder="Выберите хранилище">
                            <option value="0"></option>
                            @foreach($storages as $storage)
                                <option value="{{ $storage->id }}">
                                    {{ $storage->name }}
                                </option>
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
</div>


