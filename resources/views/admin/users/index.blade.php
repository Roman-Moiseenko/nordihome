@extends('layouts.side-menu')

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">Клиенты</h2>





    <div class="grid grid-cols-12 gap-6 mt-5">

        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">


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

                </div></form>

        </div>

        <div class="intro-y box col-span-12 p-5">
            <div class="overflow-x-auto">
        <table class="table table-hover">
            <thead class="table-dark">
            <tr>
                <th class="whitespace-nowrap">Клиент</th>
                <th class="whitespace-nowrap text-center">Последний заказ</th>
                <th class="whitespace-nowrap text-center">Кол-во заказов</th>
                <th class="whitespace-nowrap text-center">Общая сумма</th>
                <th class="whitespace-nowrap text-right">Регион</th>
                <!--th>Role</th-->
            </tr>
            </thead>
            <tbody>

            @foreach ($users as $user)
                <tr>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}">
                            <span class="font-medium">{{ $user->email }}</span>
                            <br>
                            <span class="text-slate-500 text-xs mt-0.5">{{ $user->phone }}</span>
                        </a>
                    </td>
                    <td class="text-center">
                        <span class="text-slate-500  text-xs p-1 mt-0.5 rounded-full text-white bg-success">2 дня</span>
                    </td>
                    <td class="text-center">2</td>
                    <td class="text-center">9 999 ₽</td>
                    <td class="text-right">Калининградская область</td>
                </tr>
            @endforeach

            </tbody>
        </table></div>
        </div>
    </div>
    {{ $users->links('admin.components.paginator') }}
@endsection
