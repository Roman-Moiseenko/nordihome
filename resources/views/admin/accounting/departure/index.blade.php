@extends('layouts.side-menu')

@section('subcontent')

    <h2 class="text-lg font-medium mr-auto mt-8">Списание товара</h2>

    <x-base.popover class="inline-block mt-5" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="primary" class=""
                                       id="button-supply-stack" type="button">
                    Создать Документ
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form method="post" action="{{ route('admin.accounting.departure.store') }}">
                        @csrf
                        <div class="p-2">
                            <x-base.tom-select id="select-distributor" name="storage" class=""
                                               data-placeholder="Хранилище списания">
                                <option value="0"></option>
                                @foreach($storages as $storage)
                                    <option value="{{ $storage->id }}">{{ $storage->name }}</option>
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

    <div class="mt-3">
        <livewire:admin.accounting.departure-table />
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите удалить списание?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
