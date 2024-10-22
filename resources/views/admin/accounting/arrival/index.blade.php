@extends('layouts.side-menu')

@section('subcontent')
    <h2 class="text-lg font-medium mr-auto mt-4">Поступления товара</h2>
    <div class="grid grid-cols-12 gap-4">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-4">
            <x-base.popover class="inline-block" placement="bottom-start">
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
                        <x-base.table.th class="whitespace-nowrap text-center">ЗАВЕРШЕН</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ПОСТАВЩИК</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КОЛ-ВО</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">СУММА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КОММЕНТАРИЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОТВЕТСТВЕННЫЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($arrivals as $arrival)
                        @include('admin.accounting.arrival._list', ['item' => $arrival])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    {{ $arrivals->links('admin.components.paginator') }}
    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить поступление?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
