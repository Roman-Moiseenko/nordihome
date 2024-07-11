@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Платежи
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a class="btn btn-primary shadow-md mr-2" href="{{ route('admin.sales.payment.create') }}">Создать платеж</a>
            {{ $payments->links('admin.components.count-paginator') }}

        <!-- Фильтр -->
            <div class="ml-auto">
                <x-base.popover class="inline-block mt-auto" placement="left-start">
                    <x-base.popover.button as="x-base.button" variant="primary" class="button_counter"><i data-lucide="filter" width="20" height="20"></i>
                        @if($filters['count'] > 0)
                            <span>{{ $filters['count'] }}</span>
                        @endif
                    </x-base.popover.button>
                    <x-base.popover.panel>
                        <x-base.button id="close-add-group" class="ml-auto"
                                       data-tw-dismiss="dropdown" variant="secondary" type="button">
                            X
                        </x-base.button>
                        <form action="" METHOD="GET">
                            <div class="p-2">
                                <input type="hidden" name="search" value="1" />
                                <input class="form-control" name="user" placeholder="Клиент,Телефон,ИНН,Email" value="{{ $filters['user'] }}">
                                <input class="form-control mt-1" name="order" placeholder="№ заказа" value="{{ $filters['order'] }}">

                                <x-base.tom-select id="select-staff" name="staff_id"
                                                   class="w-full bg-white mt-1" data-placeholder="Выберите ответственного">
                                    <option value="" disabled selected>Выберите ответственного</option>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ $staff->id == $filters['staff_id'] ? 'selected' : ''}} >
                                            {{ $staff->fullname->getShortName() }}
                                        </option>
                                    @endforeach
                                </x-base.tom-select>


                                <div class="flex items-center mt-3">
                                    <x-base.button id="clear-filter" class="w-32 ml-auto"
                                                   variant="secondary" type="button">
                                        Сбросить
                                    </x-base.button>
                                    <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                        Фильтр
                                    </x-base.button>
                                </div>
                            </div>
                        </form>
                    </x-base.popover.panel>
                </x-base.popover>
            </div>
        </div>
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-40 whitespace-nowrap text-center">ДАТА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">СУММА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ЗАКАЗ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КЛИЕНТ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОТВЕТСТВЕННЫЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($payments as $payment)
                        @include('admin.sales.payment._list', ['payment' => $payment])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    <div class="mt-3">
        <livewire:admin.sales.payment-table />
    </div>

    {{ $payments->links('admin.components.paginator', ['pagination' => $pagination]) }}
    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить платеж?<br>Этот процесс не может быть отменен.')->show() }}

    <script>
        let clearFilter = document.getElementById('clear-filter');
        clearFilter.addEventListener('click', function () {
            window.location.href = window.location.href.split("?")[0];
        });

    </script>

@endsection
