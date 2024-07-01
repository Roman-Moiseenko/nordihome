<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-4" wire:ignore>
            <x-base.form-label for="select-equivalent">Связанная группа аналогичных товаров</x-base.form-label>
            <x-base.tom-select id="select-equivalent" name="equivalent_id" class="w-full"
                               data-placeholder="Выберите группу аналогов товара"
                               wire:change="change" wire:model="equivalent_id"  wire:loading.attr="disabled">
                <option value="0">~ Без группы ~</option>
                @if(!is_null($equivalents))
                @foreach($equivalents as $equivalent)
                    <option value="{{ $equivalent->id }}"
                        {{ $equivalent->id == $equivalent_id ? 'selected' : ''}}
                    >
                        {{ $equivalent->name }}
                    </option>
                @endforeach
                @endif
            </x-base.tom-select>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <h3 class="font-medium text-center">Товары из группы</h3>
            <div id="equivalent-products" class="mt-3 ml-3">
                @if($equivalent_id != 0)
                    @foreach($product->equivalent_product->equivalent->products as $_product)
                        <div class="mt-1 border-b text-center text-slate-400">
                            {{ $_product->name }}
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
