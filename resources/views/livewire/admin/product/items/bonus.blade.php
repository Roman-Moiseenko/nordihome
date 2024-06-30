<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-4" wire:ignore>
            <x-searchAddProduct event="add-bonus" quantity="0" show-stock="1" published="1" caption="Добавить бонус" column="1"/>
            <div class="w-full text-slate-400 mt-6">
            </div>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <h3 class="font-medium text-center">Товары с бонусной продажей</h3>
            <div id="list-bonus">
                @foreach($product->bonus as $key => $bonus)
                    <livewire:admin.product.items.bonus-item :bonus="$bonus" :key="$bonus->id" :product="$product"/>
                @endforeach
            </div>
        </div>
    </div>
</div>
