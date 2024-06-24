@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Перемещения товара
        </h2>
    </div>
    <div class="intro-y box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3">
                <x-base.form-label for="select-storage-out">Хранилище Убытие</x-base.form-label>
                <x-base.tom-select id="select-storage-out" name="storage_out"
                                   class="w-full" data-placeholder="Выберите хранилище">
                    <option value="0"></option>
                    @foreach($storages as $storage)
                        <option value="{{ $storage->id }}"
                            {{ $storage->id == $storage_out ? 'selected' : ''}} >
                            {{ $storage->name }}
                        </option>
                    @endforeach
                </x-base.tom-select>

            </div>
            <div class="col-span-12 lg:col-span-3">
                <x-base.form-label for="select-storage-in">Хранилище Прибытие</x-base.form-label>
                <x-base.tom-select id="select-storage-in" name="storage_in"
                                   class="w-full" data-placeholder="Выберите хранилище">
                    <option value="0"></option>
                    @foreach($storages as $storage)
                        <option value="{{ $storage->id }}"
                            {{ $storage->id == $storage_in ? 'selected' : ''}} >
                            {{ $storage->name }}
                        </option>
                    @endforeach
                </x-base.tom-select>

            </div>
            <div class="col-span-12 lg:col-span-3 border-l pl-4 flex">
                <div class="">
                    <div class="form-check mr-3">
                        <input id="completed-all" class="form-check-input check-completed" type="radio" name="completed"
                               value="all" {{ $completed == 'all' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-all">Все</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-completed" class="form-check-input check-completed" type="radio" name="completed"
                               value="completed" {{ $completed == 'completed' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-completed">Завершенные</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-draft" class="form-check-input check-completed" type="radio" name="completed"
                               value="draft" {{ $completed == 'draft' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-draft">Черновики</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-departure" class="form-check-input check-completed" type="radio" name="completed"
                               value="departure" {{ $completed == 'departure' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-departure">На отбытии</label>
                    </div>
                    <div class="form-check mr-3 mt-2 sm:mt-0">
                        <input id="completed-arrival" class="form-check-input check-completed" type="radio" name="completed"
                               value="arrival" {{ $completed == 'arrival' ? 'checked' : '' }}>
                        <label class="form-check-label" for="completed-arrival">В пути</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /* Filters */
        //TODO Фильтр по дате
        const urlParams = new URLSearchParams(window.location.search);

        let selectStorageOut = document.getElementById('select-storage-out');
        selectStorageOut.addEventListener('change', function () {
            let p = selectStorageOut.options[selectStorageOut.selectedIndex].value;
            if (p === '0') {
                urlParams.delete('storage_out');
            } else {
                urlParams.set('storage_out', p);
            }
            window.location.search = urlParams;
        });
        let selectStorageIn = document.getElementById('select-storage-in');
        selectStorageIn.addEventListener('change', function () {
            let p = selectStorageIn.options[selectStorageIn.selectedIndex].value;
            if (p === '0') {
                urlParams.delete('storage_out');
            } else {
                urlParams.set('storage_out', p);
            }
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
            <button data-tw-toggle="modal" data-tw-target="#modal-create-movement" class="btn btn-primary shadow-md mr-2"
                    type="button">Создать Документ
            </button>
            {{ $movements->links('admin.components.count-paginator') }}
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-20 whitespace-nowrap">№</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">ДАТА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СТАТУС</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">УБЫТИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ПРИБЫТИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОММЕНТАРИЙ</x-base.table.th>
                        <x-base.table.th class="text-right whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($movements as $movement)
                        @include('admin.accounting.movement._list', ['movement' => $movement])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить перемещение?<br>Этот процесс не может быть отменен.')->show() }}
    {{ $movements->links('admin.components.paginator', ['pagination' => $pagination]) }}

    <x-base.dialog id="modal-create-movement" staticBackdrop>
        <x-base.dialog.panel>

            <form action="{{ route('admin.accounting.movement.store') }}" method="POST">
                @csrf
                <x-base.dialog.title>
                    <h2 class="mr-auto text-base font-medium">Создать перемещение</h2>
                </x-base.dialog.title>

                <x-base.dialog.description class="grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <x-base.form-label for="select-storage-out" class="mt-3">Хранилище Убытие</x-base.form-label>
                        <x-base.tom-select id="select-storage-out" name="storage_out" class="w-full" data-placeholder="Выберите хранилище">
                            <option value="0"></option>
                            @foreach($storages as $storage)
                                <option value="{{ $storage->id }}">
                                    {{ $storage->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>

                        <x-base.form-label for="select-storage-in" class="mt-3">Хранилище Прибытие</x-base.form-label>
                        <x-base.tom-select id="select-storage-in" name="storage_in" class="w-full" data-placeholder="Выберите хранилище">
                        <option value="0"></option>
                        @foreach($storages as $storage)
                            <option value="{{ $storage->id }}">
                                {{ $storage->name }}
                            </option>
                        @endforeach
                    </x-base.tom-select>
                    </div>
                </x-base.dialog.description>

                <x-base.dialog.footer>
                    <x-base.button id="modal-cancel" class="mr-1 w-24" data-tw-dismiss="modal" type="button" variant="outline-secondary">Отмена</x-base.button>
                    <x-base.button class="w-24" type="submit" variant="primary">Создать</x-base.button>
                </x-base.dialog.footer>
            </form>
        </x-base.dialog.panel>
    </x-base.dialog>
@endsection
