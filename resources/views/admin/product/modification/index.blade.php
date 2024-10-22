@extends('layouts.side-menu')

@section('subcontent')

    <script>

        function show_modification() {
            let inputModification = document.getElementById('modification-product');
            let other = inputModification.getAttribute('data-other');
            window.location.replace(other);
        }
    </script>
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Модификации товаров
        </h2>
    </div>
    <div class="box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3">
                <x-searchProduct route="{{ route('admin.product.modification.search', ['action' => 'index']) }}"
                                 input-data="modification-product" callback="show_modification()"/>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.modification.create') }}'">Создать Модификацию
            </button>
            {{ $modifications->links('admin.components.count-paginator') }}
        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">МОДИФИКАЦИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($modifications as $modification)
                        @include('admin.product.modification._list', ['modification' => $modification])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $modifications->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
'Вы действительно хотите расформировать группу модификации?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
