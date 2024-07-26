<div>
    <div class="box p-5 my-3">
        <div class="grid grid-cols-12 gap-x-6">
            <div class="col-span-12 lg:col-span-4">
                <label>Получатель</label>
                <div class="input-form">
                    <input type="text" class="form-control" placeholder="Фамилия" autocomplete="off"
                           wire:change="save" wire:model="surname" wire:loading.attr="disabled"
                           @if($disabled) disabled @endif>
                </div>
                <div class="input-form mt-3">
                    <input type="text" class="form-control" placeholder="Имя" autocomplete="off"
                           wire:change="save" wire:model="firstname" wire:loading.attr="disabled"
                           @if($disabled) disabled @endif>
                </div>
                <div class="input-form mt-3">
                    <input type="text" class="form-control" placeholder="Отчество" autocomplete="off"
                           wire:change="save" wire:model="secondname" wire:loading.attr="disabled"
                           @if($disabled) disabled @endif>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-4">
                <label>Контакты</label>
                <div class="input-form">
                    <input type="text" class="form-control mask-phone" placeholder="+7 (___) ___-__-__"
                           wire:change="save" wire:model="phone" wire:loading.attr="disabled" autocomplete="off"
                           @if($disabled) disabled @endif>
                </div>
                <div class="input-form mt-3">
                    <input type="text" class="form-control" placeholder="Адрес" autocomplete="off"
                           wire:change="save" wire:model="address" wire:loading.attr="disabled"
                           @if($disabled) disabled @endif>
                </div>
                <div class="flex mt-3">
                    <div class="form-check">
                        <input id="delivery-local" class="form-check-input" type="radio" name="delivery"
                               value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_LOCAL }}"

                               wire:change="save" wire:model="delivery" wire:loading.attr="disabled"
                               @if($disabled) disabled @endif
                        >
                        <label class="form-check-label" for="delivery-local">Доставка по области</label>
                    </div>
                    <div class="form-check ml-2">
                        <input id="delivery-region" class="form-check-input" type="radio" name="delivery"
                               value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_REGION }}"
                               wire:change="save" wire:model="delivery" wire:loading.attr="disabled"
                               @if($disabled) disabled @endif
                        >
                        <label class="form-check-label" for="delivery-region">Доставка по РФ</label>
                    </div>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-4">
                <div>
                    <label>Комментарий</label>
                    <textarea id="textarea-comment" name="comment" class="form-control sm:mr-2" rows="4" placeholder=""
                              wire:change="save" wire:model="comment" wire:loading.attr="disabled"
                              @if($disabled) disabled @endif></textarea>
                </div>
                <div class="mt-3">
                    Общий вес: {{ $expense->getWeight() }} кг. Общий объем: {{ $expense->getVolume() }} м3
                </div>
            </div>
        </div>
    </div>

    @if($expense->isLocal())
        <div class="box p-3 mt-3">
            @if($disabled)
                <h2 class="text-xl font-medium mb-2">Отгрузка:</h2>
                <div class="text-lg">
                    {{ $expense->calendar()->htmlDate() }} - {{ $expense->calendarPeriod()->timeHtml() }}.
                    <span class="text-success">{{ $expense->statusHtml() }}</span>
                </div>
            @else
                <livewire:admin.order.expense.calendar :expense="$expense"/>
            @endif
        </div>
    @endif
    @if($expense->isRegion() && $disabled)
        <div class="box p-3 mt-3">
            <h2 class="text-xl font-medium mb-2">Отгрузка по РФ:</h2>
            <div class="text-lg">
                Трек-номер: <span class="text-danger">{{ $expense->track }}</span>.
                <span class="text-success">{{ $expense->statusHtml() }}</span>
            </div>
        </div>
    @endif

</div>
