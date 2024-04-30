<x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
    <x-base.popover.button as="x-base.button" variant="warning" class="w-100">Установить скидку
        <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
    </x-base.popover.button>
    <x-base.popover.panel>
        <form action="{{ route('admin.sales.order.update-manual', $order) }}" METHOD="POST">
            @csrf
            <div class="p-2">
                <x-base.form-input name="manual" class="flex-1 mt-2" type="number"
                                   value="{{ $order->manual }}"
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
