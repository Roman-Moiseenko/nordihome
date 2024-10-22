@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Группы аналогов
        </h2>
    </div>
    <div class="box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3">
                <x-base.form-label for="select-category">Категории</x-base.form-label>
                <x-base.tom-select id="select-category" name="category_id"
                                   class="w-full" data-placeholder="Выберите категорию">
                    <option value="0"></option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $category->id == $category_id ? 'selected' : ''}} >
                            @for($i = 0; $i<$category->depth; $i++) - @endfor
                            {{ $category->name }}
                        </option>
                    @endforeach
                </x-base.tom-select>
            </div>
            <div class="col-span-12 lg:col-span-6 flex">
                <x-base.popover class="inline-block mt-auto" placement="bottom-start">
                    <x-base.popover.button id="button-add-equivalent" as="x-base.button" variant="primary">Добавить группу
                        <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                    </x-base.popover.button>

                    <x-base.popover.panel>
                        <form action="{{ route('admin.product.equivalent.store') }}" METHOD="POST">
                            @csrf
                            <input id="category_id" type="hidden" name="category_id" value=" autocomplete="off"">
                            <div class="p-2">
                                <div>
                                    <div class="text-xs text-left">Группа аналогов</div>
                                    <x-base.form-input name="name" class="flex-1 mt-2" type="text"
                                                       placeholder="Уникальное имя" autocomplete="off"/>
                                </div>
                                <div class="flex items-center mt-3">
                                    <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown"
                                                   variant="secondary" type="button">
                                        Отмена
                                    </x-base.button>
                                    <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                        Добавить
                                    </x-base.button>
                                </div>
                            </div>
                        </form>
                    </x-base.popover.panel>

                </x-base.popover>
            </div>
        </div>
    </div>

    <script>
        /* Filters */
        const urlParams = new URLSearchParams(window.location.search);
        let selectCategory = document.getElementById('select-category');
        if (selectCategory.options[selectCategory.selectedIndex].value === '0') {
            document.getElementById('button-add-equivalent').disabled = true;
        } else {
            document.getElementById('button-add-equivalent').disabled = false;
            document.getElementById('category_id').value = selectCategory.options[selectCategory.selectedIndex].value;
        }
        selectCategory.addEventListener('change', function () {
            let p = selectCategory.options[selectCategory.selectedIndex].value;
            urlParams.set('category_id', p);
            window.location.search = urlParams;
        });
    </script>

    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            {{ $equivalents->links('admin.components.count-paginator') }}
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ГРУППА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КОЛ-ВО ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КАТЕГОРИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($equivalents as $equivalent)
                        @include('admin.product.equivalent._list', ['equivalent' => $equivalent])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $equivalents->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить группу?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
