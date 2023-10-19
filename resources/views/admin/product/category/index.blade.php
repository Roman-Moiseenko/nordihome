@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Категории
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.category.create') }}'">Добавить Категорию
            </button>
        </div>

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
            <tr>
                <th class="whitespace-nowrap">IMG</th>
                <th class="whitespace-nowrap">ICON</th>
                <th class="whitespace-nowrap">НАЗВАНИЕ</th>
                <th class="text-center whitespace-nowrap">ВЛОЖЕННЫЕ</th>
                <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                @include('admin.product.category._list', ['category' => $category])
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить категорию?<br>Этот процесс не может быть отменен.')->show() }}

    <script>
        let elements = document.querySelectorAll(".show-children");
        Array.from(elements).forEach(function (element) {
            element.addEventListener('click', function (e) {
                e.preventDefault();
                let _show = element.getAttribute('show');
                let _for = element.getAttribute('target');
                let dropTable = document.getElementById(_for);
                let td_chevron = element.querySelector('div');
                if (_show === 'hide') {
                    element.setAttribute('show', 'visible');
                    dropTable.classList.remove('hidden');
                    td_chevron.classList.add('transform');
                    td_chevron.classList.add('rotate-180');

                } else {
                    element.setAttribute('show', 'hide');
                    dropTable.classList.add('hidden');
                    td_chevron.classList.remove('transform');
                    td_chevron.classList.remove('rotate-180');
                }
            });
        });
    </script>
@endsection
