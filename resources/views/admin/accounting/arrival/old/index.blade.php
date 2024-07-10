@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Поступления товара
        </h2>
    </div>
    <div class="box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3">
                <x-base.form-label for="select-distributor">Поставщик</x-base.form-label>
                <x-base.tom-select id="select-distributor" name="distributor_id"
                                   class="w-full" data-placeholder="Выберите поставщика">
                    <option value="0"></option>
                    @foreach($distributors as $distributor)
                        <option value="{{ $distributor->id }}"
                            {{ $distributor->id == $distributor_id ? 'selected' : ''}} >
                            {{ $distributor->name }}
                        </option>
                    @endforeach
                </x-base.tom-select>

            </div>
            <div class="col-span-12 lg:col-span-3">
                <x-base.form-label for="select-storage">Хранилище</x-base.form-label>
                <x-base.tom-select id="select-storage" name="storage_id"
                                   class="w-full" data-placeholder="Выберите хранилище">
                    <option value="0"></option>
                    @foreach($storages as $storage)
                        <option value="{{ $storage->id }}"
                            {{ $storage->id == $storage_id ? 'selected' : ''}} >
                            {{ $storage->name }}
                        </option>
                    @endforeach
                </x-base.tom-select>

            </div>
            <div class="col-span-12 lg:col-span-3 border-l pl-4 flex">
                <div class="">
                    <div class="form-check mr-3">
                        <input id="completed-all" class="form-check-input check-completed" type="radio" name="completed" value="all" {{ $completed == 'all' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-all">Все</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-true" class="form-check-input check-completed" type="radio" name="completed" value="active" {{ $completed == 'active' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-true">Проведенные</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-false" class="form-check-input check-completed" type="radio" name="completed" value="draft" {{ $completed == 'draft' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-false">Черновики</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /* Filters */
        //TODO Фильтр по дате
        const urlParams = new URLSearchParams(window.location.search);

        let selectDistributor = document.getElementById('select-distributor');
        selectDistributor.addEventListener('change', function () {
            let p = selectDistributor.options[selectDistributor.selectedIndex].value;
            urlParams.set('distributor_id', p);
            window.location.search = urlParams;
        });

        let selectStorage = document.getElementById('select-storage');
        selectStorage.addEventListener('change', function () {
            let p = selectStorage.options[selectStorage.selectedIndex].value;
            urlParams.set('storage_id', p);
            window.location.search = urlParams;
        });


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

            <x-base.popover class="inline-block mt-auto" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="primary" class=""
                                       id="button-supply-stack" type="button">
                    Создать Документ
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form method="post" action="{{ route('admin.accounting.arrival.store') }}">
                        @csrf
                        <div class="p-2">
                            <x-base.tom-select id="select-distributor" name="distributor" class=""
                                               data-placeholder="Выберите Поставщика">
                                <option value="0"></option>
                                @foreach($distributors as $distributor)
                                    <option value="{{ $distributor->id }}"
                                    >{{ $distributor->name }}</option>
                                @endforeach
                            </x-base.tom-select>

                            <div class="flex items-center mt-3">
                                <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown" variant="secondary" type="button">
                                    Отмена
                                </x-base.button>
                                <button class="w-32 ml-2 btn btn-primary" type="submit">
                                    Создать
                                </button>
                            </div>
                        </div>
                    </form>
                </x-base.popover.panel>
            </x-base.popover>

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
                        <x-base.table.th class="text-center whitespace-nowrap">КОММЕНТАРИЙ</x-base.table.th>
                        <x-base.table.th class="text-right whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
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

    <div class="mt-3">
        <livewire:admin.accounting.arrival-table />
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить поступление?<br>Этот процесс не может быть отменен.')->show() }}
    {{ $arrivals->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
