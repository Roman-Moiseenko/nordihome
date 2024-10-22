@extends('layouts.side-menu')

@section('subcontent')
    <h2 class="text-lg font-medium mr-auto mt-4">Платежи</h2>
    <div class="grid grid-cols-12 gap-4">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-4">
            <a class="btn btn-primary shadow-md mr-2" href="{{ route('admin.order.payment.create') }}">Создать платеж</a>
            {{ $payments->links('admin.components.count-paginator') }}
        <!-- Фильтр -->
            <div class="ml-auto">
                <x-tableFilter :count="$filters['count'] ?? null">
                    <input class="form-control" name="user" placeholder="Клиент,Телефон,ИНН,Email"
                           value="{{ $filters['user'] ?? '' }}" autocomplete="off">
                    <input class="form-control mt-1" name="order" placeholder="№ Заказа"
                           value="{{ $filters['order'] ?? '' }}" autocomplete="off">
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        <input type="date" name="date_begin" class="form-control" placeholder="Документы с даты"
                               value="{{ $filters['date_begin'] ?? '' }}">
                        <input type="date" name="date_end" class="form-control" placeholder="Документы до даты"
                               value="{{ $filters['date_end'] ?? '' }}">
                    </div>

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
                    <input class="form-control mt-1" name="comment" placeholder="Комментарий/Документ"
                           value="{{ $filters['comment'] ?? '' }}" autocomplete="off">

                </x-tableFilter>
            </div>
        </div>
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-40 whitespace-nowrap">ДАТА</x-base.table.th>
                        <x-base.table.th class="w-32 whitespace-nowrap text-center">СУММА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ЗАКАЗ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КЛИЕНТ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КОММЕНТАРИЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОТВЕТСТВЕННЫЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($payments as $payment)
                        @include('admin.order.payment._list', ['item' => $payment])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    {{ $payments->links('admin.components.paginator') }}
    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить платеж?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
