@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Заказы поставщикам
        </h2>
    </div>

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

            <div class="ml-auto relative label-up-button">
                <button class="btn btn-success shadow-md"
                        onclick="window.location.href='{{ route('admin.accounting.supply.stack') }}'">Стек заказов
                </button>
                @if($stack_count != 0)
                <span class="">{{ $stack_count }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-3">
        <livewire:admin.accounting.supply-table />
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить заказ?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
