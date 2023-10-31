<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-6">
        {{ \App\Forms\Input::create('name', ['placeholder' => 'Товар', 'class' => 'mt-6', 'value' => (isset($product) ? $product->name : '')])->help('Уникальное наименование до 120 символов')->show() }}
        {{ \App\Forms\Input::create('code', ['placeholder' => 'Артикул', 'class' => 'mt-3', 'value' => (isset($product) ? $product->code : '')])->help('Уникальный код (SKU)')->show() }}
        {{ \App\Forms\Input::create('slug', ['placeholder' => 'Slug', 'class' => 'mt-3', 'value' => (isset($product) ? $product->slug : '')])->help('Оставьте пустым для автоматического заполнения')->show() }}
    </div>
    <div class="col-span-12 lg:col-span-6">
        <!-- Выбрать главную категорию -->
        <x-base.form-label for="select-category">Главная категория</x-base.form-label>
        <x-base.tom-select id="select-category" name="category_id" class="w-full" data-placeholder="Выберите главную категорию">
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
        <x-base.form-label for="select-categories" class="mt-3">Доп.категории</x-base.form-label>
        <x-base.tom-select id="select-categories" name="categories[]" class="w-full" data-placeholder="Выберите вторичные категории" multiple>
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
        <!-- Выбрать бренд -->
        <x-base.form-label for="select-brand" class="mt-3">Бренд</x-base.form-label>
        <x-base.tom-select id="select-brand" name="brand_id" class="w-1/2" data-placeholder="Выберите бренд">
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

</div>
