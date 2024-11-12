@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Заказы поставщикам
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
            <div class="col-span-12 lg:col-span-3 border-l pl-4 flex">
                <div class="">
                    <div class="form-check mr-3">
                        <input id="completed-all" class="form-check-input check-completed" type="radio" name="completed"
                               value="all" {{ $completed == 'all' ? 'checked' : '' }} autocomplete="off">
                        <label class="form-check-label" for="completed-all">Все</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-created" class="form-check-input check-completed" type="radio" name="completed"
                               value="created" {{ $completed == 'created' ? 'checked' : '' }} autocomplete="off">
                        <label class="form-check-label" for="completed-created">Новые</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-sent" class="form-check-input check-completed" type="radio" name="completed"
                               value="sent" {{ $completed == 'sent' ? 'checked' : '' }} autocomplete="off">
                        <label class="form-check-label" for="completed-sent">В работе</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-completed" class="form-check-input check-completed" type="radio" name="completed"
                               value="completed" {{ $completed == 'completed' ? 'checked' : '' }} autocomplete="off">
                        <label class="form-check-label" for="completed-completed">Завершенные</label>
                    </div>
                </div>
                <div class="border-l pl-4 ">

                </div>
            </div>
        </div>
    </div>

    <script>
        /* Filters */
        const urlParams = new URLSearchParams(window.location.search);

        let selectDistributor = document.getElementById('select-distributor');
        selectDistributor.addEventListener('change', function () {
            let p = selectDistributor.options[selectDistributor.selectedIndex].value;
            urlParams.set('distributor_id', p);
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
    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <x-base.popover class="inline-block mt-auto" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="primary" class=""
                                       id="button-supply-stack" type="button">
                    Создать Документ
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form method="get" action="{{ route('admin.accounting.supply.create') }}">

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


            {{ $supplies->links('admin.components.count-paginator') }}
            <div class="ml-auto relative label-up-button">
                <button class="btn btn-success shadow-md"
                        onclick="window.location.href='{{ route('admin.accounting.supply.stack') }}'">Стек заказов
                </button>
                @if($stack_count != 0)
                <span class="">{{ $stack_count }}</span>
                @endif
            </div>


        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ДАТА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ПОСТАВЩИК</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СТАТУС</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОММЕНТАРИЙ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($supplies as $supply)
                        @include('admin.accounting.supply._list', ['supply' => $supply])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $supplies->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
