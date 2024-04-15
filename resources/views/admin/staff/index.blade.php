@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Сотрудники компании
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.staff.create') }}'">Добавить сотрудника
            </button>

            {{ $admins->links('admin.components.count-paginator') }}
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <form id="search-role-form" action="?" method="GET">
                    {{ \App\Forms\Select::create('role', ['placeholder' => 'Доступ', 'value' => $selected])->options(array_merge(['' => ''], \App\Modules\Admin\Entity\Admin::ROLES))->show() }}
                    </form>
                </div>
            </div>
        </div>
    @foreach($admins as $staff)
        @include('admin.staff._card', ['staff' => $staff])
    @endforeach
    <!-- Modal -->

    </div>

    {{ $admins->links('admin.components.paginator', ['pagination' => $pagination, 'card' => true]) }}

    {{ \App\Forms\ModalPassword::create()->show() }}

    <script>
        /** Фильтр по типу сотрудников */
        let selectRole = document.getElementById("select-role");
        let searchForm = document.getElementById("search-role-form");
        selectRole.addEventListener('change', () => {
            searchForm.submit();
        })
    </script>
@endsection

