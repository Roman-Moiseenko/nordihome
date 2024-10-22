@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Рабочие компании
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.worker.create') }}'">Добавить работника
            </button>

            {{ $workers->links('admin.components.count-paginator') }}
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <form id="search-post-form" action="?" method="GET">
                        {{ \App\Forms\Select::create('post',
                            ['placeholder' => 'Специализация', 'value' => $selected])->
                            options([0 => ''] + \App\Modules\Admin\Entity\Worker::POSTS)->
                            show() }}
                    </form>
                </div>
            </div>
        </div>
        <div class="col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
            <thead>
            <tr>
                <th class="whitespace-nowrap">ФИО</th>
                <th class="text-center whitespace-nowrap">СПЕЦИАЛИЗАЦИЯ</th>
                <th class="text-center whitespace-nowrap">ТЕЛЕФОН</th>
                <th class="text-center whitespace-nowrap">ТЕЛЕГРАМ</th>
                <th class="text-center whitespace-nowrap">СКЛАД</th>
                <th class="text-center whitespace-nowrap">АКТИВЕН</th>
                <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($workers as $worker)
                @include('admin.worker._list', ['worker' => $worker])
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

    {{ $workers->links('admin.components.paginator', ['pagination' => $pagination]) }}


    <script>
        /** Фильтр по типу сотрудников */
        let selectRole = document.getElementById("select-post");
        let searchForm = document.getElementById("search-post-form");
        selectRole.addEventListener('change', () => {
            searchForm.submit();
        })
    </script>
@endsection

