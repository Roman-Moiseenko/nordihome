@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Платежи
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            ** фильтр
        </div>
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button data-tw-toggle="modal" data-tw-target="#modal-create-order" class="btn btn-primary shadow-md mr-2"
                    type="button">Создать платеж
            </button>
            {{ $payments->links('admin.components.count-paginator') }}
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
    {{ $payments->links('admin.components.paginator', ['pagination' => $pagination]) }}
    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить платеж?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
