@extends('layouts.side-menu')

@section('subcontent')
    <h2 class="text-lg font-medium mt-4">Клиенты</h2>
    <div class="grid grid-cols-12 gap-6">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-4">
            <a class="btn btn-primary" href="{{ route('admin.home') }}">Добавить клиента ? </a>
            {{ $users->links('admin.components.count-paginator') }}
            <!-- Фильтр -->
            <div class="ml-auto">
                <x-tableFilter :count="$filters['count'] ?? null">
                    <input class="form-control" name="name" placeholder="Клиент,Телефон,ИНН,Email"
                           value="{{ $filters['name'] ?? '' }}" autocomplete="off">
                    <input class="form-control mt-1" name="address" placeholder="Адрес"
                           value="{{ $filters['address'] ?? '' }}" autocomplete="off">
                </x-tableFilter>
            </div>
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap border-b-0">
                            Клиент
                        </x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center border-b-0">
                            Последний заказ
                        </x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-center">
                            Кол-во заказов
                        </x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-center">
                            Общая сумма
                        </x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-right">
                            Регион
                        </x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach ($users as $user)
                        @include('admin.user._list', ['item' => $user])

                    @endforeach
                </x-base.table.tbody>

            </x-base.table>
        </div>
    </div>

    {{ $users->links('admin.components.paginator') }}
@endsection
