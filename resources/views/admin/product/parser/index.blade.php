@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Спарсенные Товары
        </h2>
    </div>
    <div class="box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3 border-l pl-4 flex">
                <div class="">
                    <div class="form-check mr-3">
                        <input id="published-all" class="form-check-input check-published" type="radio" name="published" value="all" {{ $published == 'all' ? 'checked' : '' }}>
                        <label class="form-check-label" for="published-all">Все</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="published-active" class="form-check-input check-published" type="radio" name="published" value="active" {{ $published == 'active' ? 'checked' : '' }}>
                        <label class="form-check-label" for="published-active">Опубликованные</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="published-draft" class="form-check-input check-published" type="radio" name="published" value="draft" {{ $published == 'draft' ? 'checked' : '' }}>
                        <label class="form-check-label" for="published-draft">Черновики</label>
                    </div>
                </div>
                <div class="border-l pl-4 ">
                Поиск по имени или артикулу
                </div>
            </div>
        </div>
    </div>

    <script>
        /* Filters */
        const urlParams = new URLSearchParams(window.location.search);

        let checkPublished = document.querySelectorAll('.check-published');
        checkPublished.forEach(function (item) {
            item.addEventListener('click', function () {
                let v = item.value;
                urlParams.set('published', v);
                window.location.search = urlParams;
            });
        })
    </script>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            {{ $parsers->links('admin.components.count-paginator') }}
        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ИКОНКА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КАТЕГОРИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО ПАЧЕК</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ЦЕНА (Zl)</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДОСТУПЕН</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СОСТАВНОЙ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">НАЛИЧИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($parsers as $parser)
                        @include('admin.product.parser._list', ['parser' => $parser])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $parsers->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
