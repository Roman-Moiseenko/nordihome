@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Создать новый товар
        </h2>
    </div>
    <form action="{{ route('admin.product.store') }}" METHOD="POST" enctype="multipart/form-data">
        @csrf
        <div class="box p-5 mt-5 block-menus-product">
            <div class="rounded-md border border-slate-200/60 p-5 dark:border-darkmode-400">

                    <div class="grid grid-cols-12 gap-x-6">
                        <div class="col-span-12 lg:col-span-6">
                            {{ \App\Forms\Input::create('name', ['placeholder' => 'Товар', 'class' => 'mt-6', 'value' => (isset($product) ? $product->name : '')])->help('Уникальное наименование до 120 символов')->show() }}
                            {{ \App\Forms\Input::create('code', ['placeholder' => 'Артикул', 'class' => 'mt-3', 'value' => (isset($product) ? $product->code : '')])->help('Уникальный код (SKU)')->show() }}
                            {{ \App\Forms\Input::create('slug', ['placeholder' => 'Slug', 'class' => 'mt-3', 'value' => (isset($product) ? $product->slug : '')])->help('Оставьте пустым для автоматического заполнения')->show() }}
                        </div>
                        <div class="col-span-12 lg:col-span-6">
                            <!-- Выбрать главную категорию -->
                            <x-base.form-label for="select-category">Главная категория</x-base.form-label>
                            <x-base.tom-select id="select-category" name="category_id" class="w-full"
                                               data-placeholder="Выберите главную категорию">
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
                            <x-base.tom-select id="select-categories" name="categories[]" class="w-full"
                                               data-placeholder="Выберите вторичные категории" multiple>
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
                                    <x-base.form-label for="select-brand" class="mt-3">Бренд</x-base.form-label>
                                    <x-base.tom-select id="select-brand" name="brand_id" class="" data-placeholder="Выберите бренд">
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
                                    <!-- Выбрать Серия -->
                                    <x-base.form-label for="select-series" class="mt-3">Серия</x-base.form-label>
                                    <x-base.tom-select id="select-series" name="series_id" class="" data-placeholder="Создайте или выберите серию (одну)" multiple>
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
        </div>

        <div class="mt-3">
            <x-base.button class="w-56 py-3" type="submit" variant="primary">Сохранить</x-base.button>
        </div>
    </form>

    <div class="relative mt-3 rounded-md border border-warning bg-warning/20 p-5">
        <x-base.lucide class="absolute top-0 right-0 mt-5 mr-3 h-12 w-12 text-warning/80" icon="Lightbulb"/>
        <h2 class="text-lg font-medium">Tips</h2>
        <div class="mt-4 font-medium">Обязательные поля</div>
        <div class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-500">
            <div>
                <b>Название товара</b> - должно быть уникально, совпадения не допускаются<br>
                <b>Артикул</b> - должно быть уникально, совпадения не допускаются, рекомендуется
                использовать от производителя<br>
                <b>Slug (Ссылка)</b> - ссылка на товар на латинице, должно быть уникально, создается автоматически, если не заполнено<br>
                <b>Главная категория</b> - необходимо выбирать конечную категорию. Промежуточные
                (имеющие дочерние) использовать не рекомендуется. Для того, чтоб товар попадал в разные
                категории, используйте "Доп.категории"<br>
                <b>Бренд</b> - если у товара нет бренда, в базе должен быть заполнен бренд типа "No name"
            </div>
        </div>
        <div class="mt-5 font-medium">Вторичные поля</div>
        <div class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-500">
            <b>Доп.категории</b> - допускается более одной дополнительной категорий<br>
            <b>Серия</b> - для группировки товаров одной серии в карточке, выберите из списка или введите новое значение<br>
        </div>
    </div>

@endsection
