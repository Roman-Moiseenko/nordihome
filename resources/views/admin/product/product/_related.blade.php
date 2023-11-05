<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-4">
        <x-base.form-label for="select-related">Поиск сопутствующих</x-base.form-label>
        <x-base.tom-select id="select-related" name="related_id" class="w-full"
                           data-placeholder="Введите наименование или артикул товара">
            <option value="0"></option>

        </x-base.tom-select>
    </div>
    <div class="col-span-12 lg:col-span-8">
        <h3 class="font-medium text-center">Список сопутствующих</h3>

    </div>
</div>
