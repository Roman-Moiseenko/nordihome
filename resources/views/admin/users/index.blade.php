@extends('layouts.side-menu')

@section('subcontent')
    <h2 class="text-lg font-medium mt-10">Клиенты</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            {{ $users->links('admin.components.count-paginator') }}
                <form action="?" method="GET">
                    <div class="flex flex-wrap sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                        <div class="w-56 relative text-slate-500 mr-2">
                            <input type="text" class="form-control w-56 box pr-10" name="city"
                                   placeholder="Поиск по городу">
                            <i data-lucide="search" width="24" height="24"
                               class="lucide lucide-search w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"></i>
                        </div>
                        <div class="w-56 relative text-slate-500 mr-2">
                            <input type="text" class="form-control w-56 box pr-10" name="name" placeholder="Поиск по имени">
                            <i data-lucide="search" width="24" height="24"
                               class="lucide lucide-search w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"></i>
                        </div>
                        <div class="relative text-slate-500">
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="filter" width="20" height="20"></i>
                            </button>
                        </div>
                    </div>
                </form>
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
                        <x-base.table.tr class="">
                            <x-base.table.td>
                                <a href="{{ route('admin.users.show', $user) }}">
                                    <span class="font-medium">{{ $user->email }}</span>
                                    <br>
                                    <span class="text-slate-500 text-xs mt-0.5">{{ $user->phone }}</span>
                                </a>
                            </x-base.table.td>
                            <x-base.table.td class="text-center">
                                @if(is_null($user->getLastOrder()))
                                    <span class="text-slate-500  text-xs p-1 mt-0.5 rounded-full text-white bg-secondary">нет</span>
                                @else
                                    @if($days=$user->getLastOrder()->created_at->diff(now())->days < 30)
                                <span class="text-slate-500  text-xs p-1 mt-0.5 rounded-full text-white bg-success">
                                    {{ $days }} дней
                                </span>
                                        @else
                                        <span class="text-slate-500  text-xs p-1 mt-0.5 rounded-full text-white bg-warning">
                                    {{ (int)($days / 30) }} месяцев
                                </span>
                                    @endif
                                @endif
                            </x-base.table.td>
                            <x-base.table.td class="text-center">
                                {{ $user->orders()->count() }}
                            </x-base.table.td>
                            <x-base.table.td class="text-center">
                                {{ price($user->getAmountOrders()) }}
                            </x-base.table.td>
                            <x-base.table.td class="text-right">
                                {{ $user->address->address }}
                            </x-base.table.td>
                        </x-base.table.tr>
                    @endforeach
                </x-base.table.tbody>

            </x-base.table>
        </div>
    </div>

    {{ $users->links('admin.components.paginator', ['pagination' => $pagination]) }}
@endsection
