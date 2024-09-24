<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-4" wire:ignore>
            <x-searchAddProduct event="add-composite" quantity="1" show-stock="1" published="1" caption="Добавить" />
            <div class="w-full text-slate-400 mt-6">
            </div>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <h3 class="font-medium text-center">Товары в составе</h3>
            <div id="list-bonus">
                @foreach($product->composites as $key => $composite)
                    <livewire:admin.product.items.composite-item :composite="$composite" :key="$composite->id" :product="$product"/>
                @endforeach
            </div>
        </div>
    </div>
</div>
