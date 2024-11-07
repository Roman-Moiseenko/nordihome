<div>
    <div class="grid grid-cols-12 gap-x-6 my-5">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">
            <h3 class="font-medium">Статус товар</h3>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="form-check form-switch ">
                <input id="checkbox-published" class="form-check-input" type="checkbox"
                       wire:model="published" wire:change="save" wire:loading.attr="disabled">
                <label class="form-check-label" for="checkbox-published">Опубликован</label>
            </div>

        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 my-5">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">
            <h3 class="font-medium">Снят с продажи</h3>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="form-check form-switch ">
                <input id="checkbox-not_sale" class="form-check-input" type="checkbox"
                       wire:model="not_sale" wire:change="save" wire:loading.attr="disabled">
                <label class="form-check-label" for="checkbox-not_sale">Включить</label>
            </div>

        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 my-5">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">
            <h3 class="font-medium">Приоритетный показ</h3>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="form-check form-switch ">
                <input id="checkbox-priority" class="form-check-input" type="checkbox"
                       wire:model="priority" wire:change="save" wire:loading.attr="disabled">
                <label class="form-check-label" for="checkbox-priority">Включить</label>
            </div>

        </div>
    </div>

    <div class="grid grid-cols-12 gap-x-6 my-5">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">
            <h3 class="font-medium">Товарный учет</h3>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="flex">
                <div class="input-group ml-0 w-full lg:w-40 ">
                    <input id="input-last-price" type="text" class="form-control" placeholder="Цена"
                           disabled autocomplete="off"
                           wire:model="price" wire:change="save" wire:loading.attr="disabled">
                    <div class="input-group-text">₽</div>
                </div>
                <div class="input-group ml-0 w-full lg:ml-4 lg:w-40 ">
                    <input id="input-count-for-sell" type="text" class="form-control"
                           disabled autocomplete="off"
                    placeholder="Кол-во" wire:model="count" wire:change="save" wire:loading.attr="disabled">
                    <div class="input-group-text">шт</div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 my-5">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">
            <h3 class="font-medium">Баланс</h3>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="flex">
                <div class="input-form ml-0 w-full lg:w-40 ">
                    <input id="input-balance_min" type="text" class="form-control" placeholder="Цена" autocomplete="off"
                           wire:model="balance_min" wire:change="save" wire:loading.attr="disabled">
                    <div class="form-help text-right">Мин.кол-во (шт)</div>
                </div>
                <div class="input-form ml-0 w-full lg:ml-4 lg:w-40 ">
                    <input id="input-balance_max" type="text" class="form-control" autocomplete="off"
                           placeholder="Кол-во" wire:model="balance_max" wire:change="save" wire:loading.attr="disabled">
                    <div class="form-help text-right">Макс.кол-во (шт)</div>
                </div>
                <div class="form-check form-switch lg:ml-4">
                    <input id="checkbox-balance_buy" class="form-check-input" type="checkbox"
                           wire:model="balance_buy" wire:change="save" wire:loading.attr="disabled">
                    <label class="form-check-label" for="checkbox-balance_buy">Закупать</label>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 my-5">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">
            <h3 class="font-medium">Продажа</h3>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="flex">
                <div class="form-check form-switch ">
                    <input id="checkbox-pre_order" class="form-check-input" type="checkbox"
                           @if(!$shop_pre_order) disabled @endif
                           wire:model="pre_order" wire:change="save" wire:loading.attr="disabled">
                    <label class="form-check-label" for="checkbox-pre_order">Возможен предзаказ</label>
                </div>

                <div class="form-check form-switch ml-3">
                    <input id="checkbox-offline" class="form-check-input" type="checkbox"
                           @if($only_offline) disabled @endif
                           wire:model="offline" wire:change="save" wire:loading.attr="disabled">
                    <label class="form-check-label" for="checkbox-offline">Продажа только офлайн</label>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 my-5">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">
            <h3 class="font-medium">Периодичность</h3>
            Используется для расчета частоты и периода показа товара и его аналогов, на основе ранее произведенных покупок
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div>
                @foreach(App\Modules\Product\Entity\Product::FREQUENCIES as $value => $caption)
                    <div class="form-check mt-2">
                        <input id="frequency-{{ $value }}" class="form-check-input" type="radio"
                               value="{{ $value }}"  wire:model="frequency" wire:change="save" wire:loading.attr="disabled">
                        <label class="form-check-label" for="frequency-{{ $value }}">{{ $caption }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
