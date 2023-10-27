@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Атрибуты
        </h2>
    </div>
    <div class="intro-y box p-5 mt-5">
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
            <div class="col-span-12 lg:col-span-3 border-l pl-4">
                <x-base.form-label for="select-group">Группы</x-base.form-label>
                <x-base.tom-select id="select-group" name="group_id" class="w-full" data-placeholder="Выберите группу">
                    <option value="0"></option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ $group->id == $group_id ? 'selected' : ''}}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </x-base.tom-select>
            </div>
            <div class="col-span-12 lg:col-span-3 flex">
                <x-base.popover class="inline-block mt-auto" placement="bottom-start">
                    <x-base.popover.button as="x-base.button" variant="primary">Добавить
                        <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                    </x-base.popover.button>

                    <x-base.popover.panel>
                        <form action="{{ route('admin.product.attribute.group-add') }}" METHOD="POST">
                            @csrf
                            <div class="p-2">
                                <div>
                                    <div class="text-xs text-left">Группа</div>
                                    <x-base.form-input name="name" class="flex-1 mt-2" type="text"
                                                       placeholder="Уникальное имя"/>
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
                <x-base.button id="groups"
                               class="w-32 mt-auto ml-6 w-auto" variant="secondary" type="button"
                               onclick="window.location.href='{{ route('admin.product.attribute.groups') }}'">
                    Управление группами
                </x-base.button>
            </div>
        </div>
    </div>

    <script>
        /* Filters */
        const urlParams = new URLSearchParams(window.location.search);
        let selectCategory = document.getElementById('select-category');
        selectCategory.addEventListener('change', function () {
            let p = selectCategory.options[selectCategory.selectedIndex].value;
            urlParams.set('category_id', p);
            window.location.search = urlParams;
        });
        let selectGroup = document.getElementById('select-group');
        selectGroup.addEventListener('change', function () {
            let p = selectGroup.options[selectGroup.selectedIndex].value;
            urlParams.set('group_id', p);
            window.location.search = urlParams;
        });

    </script>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.attribute.create') }}'">Добавить атрибут
            </button>
            {{ $prod_attributes->links('admin.components.count-paginator') }}
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ИКОНКА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">АТРИБУТ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ТИП</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ФИЛЬТР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                @foreach($prod_attributes as $prod_attribute)
                    @include('admin.product.attribute._list', ['prod_attribute' => $prod_attribute])
                @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $prod_attributes->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить атрибут?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
