<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-4">
            <select id="select-attribute" name="" class="w-full form-select mt-3" data-placeholder="Выберите атрибут"
                    wire:model="attribute_id" wire:loading.attr="disabled">
                <option value="0"></option>
                @foreach($prod_attributes as $prod_attribute)
                    @if(is_null($product->Value($prod_attribute->id)))
                    <option value="{{ $prod_attribute->id }}">{{ $prod_attribute->name }}</option>
                    @endif
                @endforeach
            </select>
            <x-base.button class="w-full mt-4" variant="primary" type="button" wire:click="add" wire:loading.attr="disabled" wire:ignore>
                <x-base.lucide class="mr-2" icon="blocks"/>
                Добавить Атрибут
            </x-base.button>
            <div class="w-full text-slate-400 mt-6">
                При изменении категории товара (главной и доп.), отсутствующие атрибуты в новых категориях будут удаляться.
            </div>
        </div>
        <div class="col-span-12 lg:col-span-8">
            @foreach($prod_attributes as $prod_attribute)
                @if(!is_null($product->Value($prod_attribute->id)))
                    <livewire:admin.product.items.attribute-item :product="$product" :key="$prod_attribute->id" :attribute="$prod_attribute">
                @endif
            @endforeach
        </div>
    </div>
</div>


