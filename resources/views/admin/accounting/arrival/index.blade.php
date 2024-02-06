@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Поступления товара
        </h2>
    </div>
    <div class="intro-y box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3">

            </div>
            <div class="col-span-12 lg:col-span-3 border-l pl-4 flex">
                <div class="">
                    <div class="form-check mr-3">
                        <input id="completed-all" class="form-check-input check-completed" type="radio" name="completed" value="all" {{ $completed == 'all' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-all">Все</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-true" class="form-check-input check-completed" type="radio" name="completed" value="active" {{ $completed == 'true' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-true">Проведенные</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-false" class="form-check-input check-completed" type="radio" name="completed" value="draft" {{ $completed == 'false' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-false">Черновики</label>
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
        //TODO Фильтр по дате

        let checkPublished = document.querySelectorAll('.check-completed');
        checkPublished.forEach(function (item) {
            item.addEventListener('click', function () {
                let v = item.value;
                urlParams.set('completed', v);
                window.location.search = urlParams;
            });
        })
    </script>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.accounting.arrival.create') }}'">Создать Документ
            </button>
            {{ $arrivals->links('admin.components.count-paginator') }}
        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ДАТА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ПОСТАВЩИК</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ХРАНИЛИЩЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СУММА ЗАКУПА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($arrivals as $arrival)
                        @include('admin.accounting.arrival._list', ['arrival' => $arrival])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $arrivals->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
