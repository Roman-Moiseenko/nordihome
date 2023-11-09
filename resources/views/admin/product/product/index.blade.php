@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Товары
        </h2>
    </div>
    <div class="intro-y box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3">
                <x-base.form-label for="select-category">Категории</x-base.form-label>
                <x-base.tom-select id="select-category" name="category_id"
                                   class="w-full" data-placeholder="Выберите категорию">
                    <option value="0"></option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $category->id == $category_id ? 'selected' : ''}} >
                            @for($i = 0; $i<$category->depth; $i++) - @endfor
                            {{ $category->name }}
                        </option>
                    @endforeach
                </x-base.tom-select>
            </div>
            <div class="col-span-12 lg:col-span-3 border-l pl-4">
                Поиск по имени или артикулу

            </div>
        </div>
    </div>

    <script>
        /* Filters */
        const urlParams = new URLSearchParams(window.location.search);
        let selectCategory = document.getElementById('select-category');
        selectCategory.addEventListener('change', function () {
            let p = selectCategory.options[selectCategory.selectedIndex].value;
            urlParams.set('category_id', p);
            window.location.search = urlParams;
        });

    </script>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.create') }}'">Создать товар
            </button>
            {{ $products->links('admin.components.count-paginator') }}
        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ИКОНКА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КАТЕГОРИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ЧТО_ТО_ЕЩЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
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

@endsection
