@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Товары
            @if($filters['count'] > 0)
                - <em>[{{ $filters['text'] }}]</em>
            @endif
        </h2>
    </div>
    @if(false)
    <div class="intro-y box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3">
                <x-base.form-label for="select-category">Категории</x-base.form-label>
                <x-base.tom-select id="select-category" name="category_id"
                                   class="w-full" data-placeholder="Выберите категорию">
                    <option value="0"></option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $category->id == $filters['category'] ? 'selected' : ''}} >
                            @for($i = 0; $i<$category->depth; $i++) - @endfor
                            {{ $category->name }}
                        </option>
                    @endforeach
                </x-base.tom-select>
            </div>
            <div class="col-span-12 lg:col-span-2 border-l pl-4 flex">
                <div class="">
                    <div class="form-check mr-3">
                        <input id="published-all" class="form-check-input check-published" type="radio" name="published" value="all" {{ $filters['published'] == 'all' ? 'checked' : '' }}>
                        <label class="form-check-label" for="published-all">Все</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="published-active" class="form-check-input check-published" type="radio" name="published" value="active" {{ $filters['published'] == 'active' ? 'checked' : '' }}>
                        <label class="form-check-label" for="published-active">Опубликованные</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="published-draft" class="form-check-input check-published" type="radio" name="published" value="draft" {{ $filters['published'] == 'draft' ? 'checked' : '' }}>
                        <label class="form-check-label" for="published-draft">Черновики</label>
                    </div>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-4 border-l pl-4 flex flex-col">
                <x-base.form-label>Поиск товара по названию или артикулу</x-base.form-label>
                <x-searchProduct id="search-product-select" route="{{ route('admin.product.search') }}"
                                 input-data="product-product" hidden-id="product_id" class="w-full"  callback="_callback()"/>
            </div>
        </div>
    </div>
    @endif
    <script>
        /* Filters */
        const urlParams = new URLSearchParams(window.location.search);

        let selectCategory = document.getElementById('select-category');
        selectCategory.addEventListener('change', function () {
            let p = selectCategory.options[selectCategory.selectedIndex].value;
            urlParams.set('category_id', p);
            window.location.search = urlParams;
        });

        let checkPublished = document.querySelectorAll('.check-published');
        checkPublished.forEach(function (item) {
            item.addEventListener('click', function () {
                let v = item.value;
                urlParams.set('published', v);
                window.location.search = urlParams;
            });
        });

        function _callback() {
            window.location.href = document.getElementById('product-product').dataset.url;
        }
    </script>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.create') }}'">Создать товар
            </button>
            {{ $products->links('admin.components.count-paginator') }}

        <!-- Фильтр -->
            <div class="ml-auto">
                <x-base.popover class="inline-block mt-auto" placement="left-start">
                    <x-base.popover.button as="x-base.button" variant="primary" class="button_counter"><i data-lucide="filter" width="20" height="20"></i>
                        @if($filters['count'] > 0)
                            <span>{{ $filters['count'] }}</span>
                        @endif
                    </x-base.popover.button>
                    <x-base.popover.panel>
                        <x-base.button id="close-add-group" class="ml-auto"
                                       data-tw-dismiss="dropdown" variant="secondary" type="button">
                            X
                        </x-base.button>
                        <form action="" METHOD="GET">
                            <div class="p-2">
                                <input class="form-control" name="product" placeholder="Название, Артикул, Серия" value="{{ $filters['product'] }}">

                                <x-base.tom-select id="select-category" name="category_id"
                                                   class="w-full mt-3" data-placeholder="Выберите категорию">
                                    <option value="" disabled selected>Выберите категорию</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == $filters['category'] ? 'selected' : ''}} >
                                            @for($i = 0; $i<$category->depth; $i++) - @endfor
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </x-base.tom-select>

                                <div class="mt-3">
                                    <div class="form-check mr-3">
                                        <input id="published-all" class="form-check-input check-published" type="radio" name="published" value="all" {{ $filters['published'] == 'all' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="published-all">Все</label>
                                    </div>
                                    <div class="form-check mr-3 mt-2 sm:mt-0">
                                        <input id="published-active" class="form-check-input check-published" type="radio" name="published" value="active" {{ $filters['published'] == 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="published-active">Опубликованные</label>
                                    </div>
                                    <div class="form-check mr-3 mt-2 sm:mt-0">
                                        <input id="published-draft" class="form-check-input check-published" type="radio" name="published" value="draft" {{ $filters['published'] == 'draft' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="published-draft">Черновики</label>
                                    </div>
                                </div>

                                <div class="flex items-center mt-3">
                                    <x-base.button id="clear-filter" class="w-32 ml-auto"
                                                   variant="secondary" type="button">
                                        Сбросить
                                    </x-base.button>
                                    <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                        Фильтр
                                    </x-base.button>
                                </div>
                            </div>
                        </form>
                    </x-base.popover.panel>
                </x-base.popover>
            </div>
        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-10 text-center whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="w-32 text-center whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="w-40 text-center whitespace-nowrap">КАТЕГОРИЯ</x-base.table.th>
                        <x-base.table.th class="w-32 text-center whitespace-nowrap">ЦЕНА</x-base.table.th>
                        <x-base.table.th class="w-32 text-center whitespace-nowrap">НАЛИЧИЕ</x-base.table.th>
                        <x-base.table.th class="text-right whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($products as $product)
                        @include('admin.product.product._list', ['product' => $product])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $products->links('admin.components.paginator', ['pagination' => $pagination]) }}
<script>
    let clearFilter = document.getElementById('clear-filter');
    clearFilter.addEventListener('click', function () {
        window.location.href = window.location.href.split("?")[0];
    });
</script>
@endsection
