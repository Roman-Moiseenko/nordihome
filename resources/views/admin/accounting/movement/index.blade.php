@extends('layouts.side-menu')

@section('subcontent')

    <h2 class="text-lg font-medium mr-auto mt-4">Перемещения товара</h2>
    <div class="grid grid-cols-12 gap-6">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-4">
            <button data-tw-toggle="modal" data-tw-target="#modal-create-movement" class="btn btn-primary shadow-md mr-2"
                    type="button">Создать Документ
            </button>
        {{ $movements->links('admin.components.count-paginator') }}
        <!-- Фильтр -->
            <div class="ml-auto">
                <x-tableFilter :count="$filters['count'] ?? null">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" name="date_begin" class="form-control" placeholder="Документы с даты"
                               value="{{ $filters['date_begin'] ?? '' }}">
                        <input type="date" name="date_end" class="form-control" placeholder="Документы до даты"
                               value="{{ $filters['date_end'] ?? '' }}">
                    </div>
                    <x-base.tom-select class="w-full bg-white mt-1" name="storage_out"
                                       data-placeholder="Склад Убытия">
                        <option value="" disabled selected>Склад Убытия</option>
                        @foreach($storages as $storage)
                            <option value="{{ $storage->id }}"
                            @if(isset($filters['storage_out'])) {{ $storage->id == $filters['storage_out'] ? 'selected' : ''}} @endif
                            >{{ $storage->name }}</option>
                        @endforeach
                    </x-base.tom-select>
                    <x-base.tom-select class="w-full bg-white mt-1" name="storage_in"
                                       data-placeholder="Склад Прибытия">
                        <option value="" disabled selected>Склад Прибытия</option>
                        @foreach($storages as $storage)
                            <option value="{{ $storage->id }}"
                            @if(isset($filters['storage_in'])) {{ $storage->id == $filters['storage_in'] ? 'selected' : ''}} @endif
                            >{{ $storage->name }}</option>
                        @endforeach
                    </x-base.tom-select>
                    <x-base.tom-select id="select-staff" name="staff_id"
                                       class="w-full bg-white mt-1" data-placeholder="Выберите ответственного">
                        <option value="" disabled selected>Выберите ответственного</option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}"
                            @if(isset($filters['staff_id']))
                                {{ $staff->id == $filters['staff_id'] ? 'selected' : ''}}
                                @endif
                            >
                                {{ $staff->fullname->getShortName() }}
                            </option>
                        @endforeach
                    </x-base.tom-select>

                    <x-base.tom-select class="w-full bg-white mt-1" name="status"
                                       data-placeholder="Статус документа">
                        <option value="" disabled selected>Статус документа</option>
                        @foreach($statuses as $value => $name)
                            <option value="{{ $value }}"
                            @if(isset($filters['status'])) {{ $value == $filters['status'] ? 'selected' : ''}} @endif
                            >{{ $name }}</option>
                        @endforeach
                    </x-base.tom-select>
                    <input class="form-control mt-1" name="comment" placeholder="Комментарий"
                           value="{{ $filters['comment'] ?? '' }}" autocomplete="off">
                    <div class="mt-2">
                        <div class="form-check mr-3 mt-3 sm:mt-0">
                            <input id="draft" class="form-check-input" type="checkbox" name="draft"
                                {{ isset($filters['draft']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="draft">Не завершен</label>
                        </div>
                    </div>

                </x-tableFilter>
            </div>
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-32 whitespace-nowrap text-center">ДАТА</x-base.table.th>
                        <x-base.table.th class="w-32 whitespace-nowrap">№ ДОКУМЕНТА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">СТАТУС</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">УБЫТИЕ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ПРИБЫТИЯ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КОЛ-ВО ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КОММЕНТАРИЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОТВЕТСТВЕННЫЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($movements as $movement)
                        @include('admin.accounting.movement._list', ['item' => $movement])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>

    </div>
    {{ $movements->links('admin.components.paginator') }}
    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить перемещение?<br>Этот процесс не может быть отменен.')->show() }}

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
