<div class="create-add-product">
    <button data-tw-toggle="modal" data-tw-target="#modal-create-order" class="btn btn-outline-primary ml-2"
            type="button">Создать товар
    </button>
    <span id="data" data-route="{{ $routeCreate }}" data-token="{{ csrf_token() }}" ></span>
    <x-base.dialog id="modal-create-order" staticBackdrop>
        <x-base.dialog.panel>

                <x-base.dialog.title>
                    <h2 class="mr-auto text-base font-medium">Создать товар</h2>
                </x-base.dialog.title>

                <x-base.dialog.description class="grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                    {{ \App\Forms\Input::create('name', ['placeholder' => 'Товар', 'class' => 'mt-6'])->help('Уникальное наименование до 120 символов')->show() }}
                    {{ \App\Forms\Input::create('code', ['placeholder' => 'Артикул', 'class' => 'mt-3'])->help('Уникальный код (SKU)')->show() }}
                    </div>
                    <div class="col-span-12">

                        <x-base.form-label for="select-category">Главная категория</x-base.form-label>
                        <x-base.tom-select id="select-category" name="category_id" class="w-full"
                                           data-placeholder="Выберите главную категорию">
                            <option value="0"></option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    @for($i = 0; $i<$category->depth; $i++) - @endfor
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>

                        <!-- Выбрать бренд -->
                        <x-base.form-label for="select-brand" class="mt-3">Бренд</x-base.form-label>
                        <x-base.tom-select id="select-brand" name="brand_id" class="" data-placeholder="Выберите бренд">
                            <option value="0"></option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </x-base.tom-select>

                        <label for="input-price" class="inline-block mt-2">
                            Розничная цена
                        </label>
                        <div class="input-group ml-0 w-full lg:w-40 mt-1">

                            <input id="input-price" type="text" class="form-control" placeholder=""
                                   autocomplete="off" name="price"
                                   >
                            <div class="input-group-text">₽</div>
                        </div>
                    </div>
                </x-base.dialog.description>

                <x-base.dialog.footer>
                    <x-base.button id="modal-cancel" class="mr-1 w-24" data-tw-dismiss="modal" type="button" variant="outline-secondary">Отмена</x-base.button>
                    <button id="create-product" class="w-24 btn btn-primary" type="button" data-route="{{ $route }}"
                            data-event="{{ $event }}">Создать</button>
                </x-base.dialog.footer>
        </x-base.dialog.panel>
    </x-base.dialog>
    @once
        @push('scripts')
            @vite('resources/js/components/create-add-product.js')
        @endpush
    @endonce
</div>
