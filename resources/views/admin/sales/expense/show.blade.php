@extends('layouts.side-menu')

@section('subcontent')

    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                Распоряжение {{ $expense->htmlNum() . ' от ' . $expense->created_at->format('d-M-Y') }}
            </h1>
        </div>
    </div>
    <div class="box p-3 mt-3">
        @if($expense->isNew())
            <button class="btn btn-primary mr-3">На сборку</button>
            <button class="btn btn-secondary" type="button" data-tw-toggle="modal"
                    data-tw-target="#cancel-confirmation-modal"
                    data-route = {{ route('admin.sales.expense.destroy', $expense) }}>
                Отменить
            </button>
        @endif

        @if($expense->isCompleted())
            <span class="font-medium">Заказ выдан</span>
        @endif
    </div>

    <div class="box col-span-12 overflow-auto lg:overflow-visible p-4 mt-3">
        <x-base.table class="table table-hover">
            <x-base.table.thead class="table-dark">
                <x-base.table.tr>
                    <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ЯЧЕЙКА ХРАНЕНИЯ</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ПРИМЕЧАНИЕ</x-base.table.th>
                </x-base.table.tr>
            </x-base.table.thead>
            <x-base.table.tbody>
                @foreach($expense->items as $item)
                    @include('admin.sales.expense._items', ['item' => $item])
                @endforeach
            </x-base.table.tbody>
        </x-base.table>
    </div>

    <div class="box col-span-12 overflow-auto lg:overflow-visible p-4 mt-3">
        <x-base.table class="table table-hover">
            <x-base.table.thead class="table-dark">
                <x-base.table.tr>
                    <x-base.table.th class="w-56 whitespace-nowrap">УСЛУГА</x-base.table.th>
                    <x-base.table.th class="w-40 whitespace-nowrap text-center">СУММА</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ПРИМЕЧАНИЕ</x-base.table.th>
                </x-base.table.tr>
            </x-base.table.thead>
            <x-base.table.tbody>
                @foreach($expense->additions as $addition)
                    @include('admin.sales.expense._additions', ['addition' => $addition])
                @endforeach
            </x-base.table.tbody>
        </x-base.table>
    </div>

    <div class="grid grid-cols-12 gap-x-6 pb-20">

    </div>

    <div class="grid grid-cols-12 gap-x-6 pb-20">

    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите отменить Распоряжение?<br>Этот процесс не может быть отменен.', 'cancel-confirmation-modal')->show() }}
@endsection
