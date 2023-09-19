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
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>

                </div></form>

        </div>


        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <!--th>Role</th-->
            </tr>
            </thead>
            <tbody>

            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->isWait())
                            <span class="badge bg-secondary">Waiting</span>
                        @endif
                        @if ($user->isActive())
                            <span class="badge bg-primary">Active</span>
                        @endif
                    </td>
                    <!-- role -->
                </tr>
            @endforeach

            </tbody>
        </table>

    </div>
    {{ $users->links('admin.components.paginator') }}
@endsection
