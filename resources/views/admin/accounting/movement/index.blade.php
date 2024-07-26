@extends('layouts.side-menu')

@section('subcontent')

    <h2 class="text-lg font-medium mr-auto mt-8">Перемещения товара</h2>

    <button data-tw-toggle="modal" data-tw-target="#modal-create-movement" class="btn btn-primary shadow-md mr-2 mt-5"
            type="button">Создать Документ
    </button>

    <div class="mt-3">
        <livewire:admin.accounting.movement-table />
    </div>

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
