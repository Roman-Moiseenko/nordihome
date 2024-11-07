@extends('layouts.side-menu')

@section('subcontent')

    <h2 class="text-lg font-medium mr-auto mt-4">Заказы поставщикам</h2>

    <div class="grid grid-cols-12 gap-4">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-4">
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
            <div class="ml-3 relative label-up-button">
                <button class="btn btn-success shadow-md"
                        onclick="window.location.href='{{ route('admin.accounting.supply.stack') }}'">Стек заказов
                </button>
                @if($stack_count != 0)
                <span class="">{{ $stack_count }}</span>
                @endif
            </div>
            {{ $supplies->links('admin.components.count-paginator') }}

            <!-- Фильтр -->
            <div class="ml-auto">
                <x-tableFilter :count="$filters['count'] ?? null">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" name="date_begin" class="form-control" placeholder="Документы с даты"
                               value="{{ $filters['date_begin'] ?? '' }}">
                        <input type="date" name="date_end" class="form-control" placeholder="Документы до даты"
                               value="{{ $filters['date_end'] ?? '' }}">
                    </div>
                    <x-base.tom-select class="w-full bg-white mt-1" name="distributor"
                                       data-placeholder="Поставщик">
                        <option value="" disabled selected>Поставщик</option>
                        @foreach($distributors as $distributor)
                            <option value="{{ $distributor->id }}"
                            @if(isset($filters['distributor'])) {{ $distributor->id == $filters['distributor'] ? 'selected' : ''}} @endif
                            >{{ $distributor->name }}</option>
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
                    <input class="form-control mt-1" name="comment" placeholder="Комментарий"
                           value="{{ $filters['comment'] ?? '' }}" autocomplete="off">
                </x-tableFilter>
            </div>

        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-32 whitespace-nowrap text-center">ДАТА</x-base.table.th>
                        <x-base.table.th class="w-32 whitespace-nowrap">№ ДОКУМЕНТА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ПОСТАВЩИК</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ПРОВЕДЕН</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КОЛ-ВО</x-base.table.th>

                        <x-base.table.th class="whitespace-nowrap text-center">КОММЕНТАРИЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОТВЕТСТВЕННЫЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($supplies as $supply)
                        @include('admin.accounting.supply._list', ['item' => $supply])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $supplies->links('admin.components.paginator') }}
    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить заказ?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
