@extends('layouts.side-menu')

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">Сотрудники компании</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2" onclick="window.location.href='{{ route('admin.staff.create') }}'">Добавить сотрудника</button>

            {{ $admins->links('admin.components.count-paginator') }}
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" placeholder="Search...">
                    <i data-lucide="search" width="24" height="24" class="lucide lucide-search w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"></i>
                </div>
            </div>
        </div>
        @foreach($admins as $staff)
            @include('admin.components.cards.user3', ['staff' => $staff])
        @endforeach
    </div>
    {{ $admins->links('admin.components.paginator') }}
@endsection

