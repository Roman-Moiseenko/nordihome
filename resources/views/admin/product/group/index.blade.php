@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Группы товаров
        </h2>
    </div>
    <div class="box p-5 mt-5">
        <form method="GET" action="{{ route('admin.product.group.index') }}">
        <div class="flex">
                <input class="form-control w-52" name="search" value="{{ old('search') ?? '' }}">
                <x-base.button id="groups" class="w-32 mt-auto ml-6 w-auto" variant="secondary" type="submit">
                    Поиск
                </x-base.button>
        </div>
        </form>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.group.create') }}'">Создать группу
            </button>
            {{ $groups->links('admin.components.count-paginator') }}
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">НАЗВАНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($groups as $group)
                        @include('admin.product.group._list', ['group' => $group])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $groups->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить атрибут?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
