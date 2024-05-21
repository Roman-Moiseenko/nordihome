@extends('layouts.side-menu')

@section('subcontent')

    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                Распоряжение {{ $expense->htmlNum() . ' от ' . $expense->htmlDate() }}
            </h1>
        </div>
    </div>
    <div class="box p-3 mt-3">
        @if($expense->isNew())
            <button class="btn btn-primary mr-3" onclick="document.getElementById('form-expense-assembly').submit();">На
                сборку
            </button>
            <button class="btn btn-secondary" type="button" data-tw-toggle="modal"
                    data-tw-target="#cancel-confirmation-modal"
                    data-route= {{ route('admin.sales.expense.destroy', $expense) }}>
                Отменить
            </button>
            <form id="form-expense-assembly" method="post"
                  action="{{ route('admin.sales.expense.assembly', $expense) }}">
                @csrf
            </form>
        @endif

        @if($expense->isAssembly())
            <button class="btn btn-primary mr-3">Распечатать накладную</button>
        @endif

            <span class="font-medium text-center text-lg ml-3 text-primary">{{ $expense->statusHtml() }}</span>

    </div>

    <div class="box col-span-12 overflow-auto lg:overflow-visible p-4 mt-3">
        <x-base.table class="table table-hover">
            <x-base.table.thead class="table-dark">
                <x-base.table.tr>
                    <x-base.table.th class="whitespace-nowrap"></x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                    <x-base.table.th class="text-center whitespace-nowrap">ТОВАР</x-base.table.th>
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

    @if(!$expense->isStorage())
        <livewire:admin.sales.expense.delivery :expense="$expense" :disabled="$expense->isCompleted()"/>
    @endif


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите отменить Распоряжение?<br>Этот процесс не может быть отменен.', 'cancel-confirmation-modal')->show() }}
@endsection
