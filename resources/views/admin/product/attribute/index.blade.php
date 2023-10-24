@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Атрибуты
        </h2>
    </div>

    <div class="flex mt-5">
        <div class="col-span-1 items-center flex mr-6">
            <span class="text-base font-medium my-auto">Фильтрация</span>
        </div>

            <select id="select-category" name="category_id" class="tom-select w-72 mt-3 sm:mt-0">
                <option value="0"></option>
                @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                                {{ $category->id == $category_id ? 'selected' : ''}} >
                                @for($i = 0; $i<$category->depth; $i++) - @endfor
                            {{ $category->name }}
                        </option>
                @endforeach
            </select>
        <select id="select-group" name="group_id" class="tom-select w-72 ml-3 sm:mt-0">
            <option value="0"></option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ $group->id == $group_id ? 'selected' : ''}}>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>
        <div class="text-center">
            <div id="dropdown-group-add" class="dropdown inline-block" data-tw-placement="bottom-start"> <button class="dropdown-toggle btn btn-primary" aria-expanded="false" data-tw-toggle="dropdown"> Filter Dropdown <i data-lucide="chevron-down" class="w-4 h-4 ml-2"></i> </button>
                <div class="dropdown-menu">
                    <div class="dropdown-content">
                        <div class="p-2">
                            <div>
                                <div class="text-xs">From</div> <input type="text" class="form-control mt-2 flex-1" placeholder="example@gmail.com" />
                            </div>
                            <div class="mt-3">
                                <div class="text-xs">To</div> <input type="text" class="form-control mt-2 flex-1" placeholder="example@gmail.com" />
                            </div>
                            <div class="flex items-center mt-3"> <button data-dismiss="dropdown" class="btn btn-secondary w-32 ml-auto">Close</button> <button class="btn btn-primary w-32 ml-2">Search</button> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
//        const myDropdown = tailwind.Dropdown.getInstance(document.querySelector("#dropdown-group-add"));
        let selectCategory = document.getElementById('select-category');
        selectCategory.addEventListener('change', function () {
            let p = selectCategory.options[selectCategory.selectedIndex].value;
            urlParams.set('category_id', p);
            window.location.search = urlParams;
        });
        let selectGroup= document.getElementById('select-group');
        selectGroup.addEventListener('change', function () {
            let p = selectGroup.options[selectGroup.selectedIndex].value;
            urlParams.set('group_id', p);
            myDropdown.hide();
            //window.location.search = urlParams;
        });

        //
    </script>


    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.attribute.create') }}'">Добавить атрибут
            </button>
            {{ $prod_attributes->links('admin.components.count-paginator') }}
        </div>

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
        <table class="table -mt-2">
            <thead>
            <tr>
                <th class="whitespace-nowrap">ИКОНКА</th>
                <th class="whitespace-nowrap">АТРИБУТ</th>
                <th class="text-center whitespace-nowrap">ТИП</th>
                <th class="text-center whitespace-nowrap"></th>
                <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($prod_attributes as $prod_attribute)
                @include('admin.product.attribute._list', ['prod_attribute' => $prod_attribute])
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

    {{ $prod_attributes->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить атрибут?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
