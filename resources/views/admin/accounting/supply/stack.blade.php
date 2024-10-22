@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Стек заказов
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-4 mt-5">
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ОСНОВАНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">МЕНЕДЖЕР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ПОСТАЩИКИ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($stacks as $stack)
                        @include('admin.accounting.supply._stack-item', ['stack' => $stack])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите отменить Распоряжение?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
