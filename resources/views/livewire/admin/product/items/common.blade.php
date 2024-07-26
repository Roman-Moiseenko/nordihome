<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-6">
            <div class="input-form mt-6 ">
                <input id="input-name" type="text" class="form-control @if(isset($errors['name'])) border-danger @endif" placeholder="Товар"
                       wire:model="name" wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                <div class="form-help text-right">Уникальное наименование до 120 символов</div>
            </div>

            <div class="input-form mt-3 ">
                <input id="input-code" type="text" class="form-control @if(isset($errors['code'])) border-danger @endif" placeholder="Артикул"
                       wire:model="code" wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                <div class="form-help text-right">Уникальный код (SKU)</div>
            </div>
            <div class="input-form mt-3 ">
                <input id="input-slug" type="text" class="form-control @if(isset($errors['slug'])) border-danger @endif" placeholder="Slug"
                       wire:model="slug" wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                <div class="form-help text-right">Оставьте пустым для автоматического заполнения</div>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-6" wire:ignore>
            <!-- Выбрать главную категорию -->
            <x-base.form-label for="select-category-component">Главная категория</x-base.form-label>
            <x-base.tom-select id="select-category-component" class="w-full"
                               data-placeholder="Выберите главную категорию"
                               wire:model="category_id" wire:change="change_category" wire:loading.attr="disabled"
            >
                <option value="0"></option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                    @if(isset($product))
                        {{ $category->id == $product->category->id ? 'selected' : ''}}
                        @endif
                    >
                        @for($i = 0; $i<$category->depth; $i++) - @endfor
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-base.tom-select>

            <!-- Выбрать категории -->
            <x-base.form-label for="select-categories-component" class="mt-3">Доп.категории</x-base.form-label>
            <x-base.tom-select id="select-categories-component" class="w-full"
                               data-placeholder="Выберите вторичные категории" multiple
                               wire:model="_categories" wire:change="change_categories" wire:loading.attr="disabled">
                <option value="0"></option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                    @if(isset($product))
                        {{ $product->isCategories($category->id) ? 'selected' : ''}}
                        @endif
                    >
                        @for($i = 0; $i<$category->depth; $i++) - @endfor
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-base.tom-select>
            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-6">
                    <!-- Выбрать бренд -->
                    <x-base.form-label for="select-brand-component" class="mt-3">Бренд</x-base.form-label>
                    <x-base.tom-select id="select-brand-component" data-placeholder="Выберите бренд"
                                       wire:model="brand_id" wire:change="change_brand" wire:loading.attr="disabled">
                        <option value="0"></option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                            @if(isset($product))
                                {{ $brand->id == $product->brand_id ? 'selected' : ''}}
                                @endif
                            >{{ $brand->name }}</option>
                        @endforeach
                    </x-base.tom-select>
                </div>
                <div class="col-span-12 lg:col-span-6">
                    <!-- Выбрать бренд -->
                    <x-base.form-label for="select-series-component" class="mt-3">Серия</x-base.form-label>
                    <x-base.tom-select id="select-series-component" class="" data-placeholder="Создайте или выберите серию (одну)" multiple
                                       wire:model="series_id" wire:change="change_series" wire:loading.attr="disabled"
                    >
                        <option value="0"></option>
                        @foreach($series as $item_series)
                            <option value="{{ $item_series->id }}"
                            @if(isset($product))
                                {{ $product->isSeries($item_series->id) ? 'selected' : ''}}
                                @endif
                            >{{ $item_series->name }}</option>
                        @endforeach
                    </x-base.tom-select>
                </div>

            </div>
        </div>

    </div>

</div>
