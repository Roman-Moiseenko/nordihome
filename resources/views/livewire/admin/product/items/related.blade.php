<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-4" wire:ignore>
            <x-searchAddProduct event="add-related" quantity="0" show-stock="1" published="1" caption="Добавить Аксессуар" column="1"/>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <h3 class="font-medium text-center">Список сопутствующих</h3>
            <div id="list-related">
                @foreach($product->related as $related)
                    <livewire:admin.product.items.related-item :related="$related" :key="$related->id" :product="$product"/>
                @endforeach
            </div>
        </div>
    </div>
</div>
