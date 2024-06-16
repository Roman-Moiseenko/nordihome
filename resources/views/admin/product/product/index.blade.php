@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Товары
            @if($filters['count'] > 0)
                - <em>[{{ $filters['text'] }}]</em>
            @endif
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.create') }}'">Создать товар
            </button>
            {{ $products->links('admin.components.count-paginator') }}

        <!-- Фильтр -->
            <div class="ml-auto">
                <x-base.popover class="inline-block mt-auto" placement="left-start">
                    <x-base.popover.button as="x-base.button" variant="primary" class="button_counter"><i data-lucide="filter" width="20" height="20"></i>
                        @if($filters['count'] > 0)
                            <span>{{ $filters['count'] }}</span>
                        @endif
                    </x-base.popover.button>
                    <x-base.popover.panel>
                        <x-base.button id="close-add-group" class="ml-auto"
                                       data-tw-dismiss="dropdown" variant="secondary" type="button">
                            X
                        </x-base.button>
                        <form action="" METHOD="GET">
                            <div class="p-2">
                                <input class="form-control" name="product" placeholder="Название, Артикул, Серия" value="{{ $filters['product'] }}">

                                <x-base.tom-select id="select-category" name="category_id"
                                                   class="w-full mt-3" data-placeholder="Выберите категорию">
                                    <option value="" disabled selected>Выберите категорию</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == $filters['category'] ? 'selected' : ''}} >
                                            @for($i = 0; $i<$category->depth; $i++) - @endfor
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </x-base.tom-select>

                                <div class="mt-3">
                                    <div class="form-check mr-3">
                                        <input id="published-all" class="form-check-input check-published" type="radio" name="published" value="all" {{ $filters['published'] == 'all' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="published-all">Все</label>
                                    </div>
                                    <div class="form-check mr-3 mt-2 sm:mt-0">
                                        <input id="published-active" class="form-check-input check-published" type="radio" name="published" value="active" {{ $filters['published'] == 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="published-active">Опубликованные</label>
                                    </div>
                                    <div class="form-check mr-3 mt-2 sm:mt-0">
                                        <input id="published-draft" class="form-check-input check-published" type="radio" name="published" value="draft" {{ $filters['published'] == 'draft' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="published-draft">Черновики</label>
                                    </div>
                                </div>

                                <div class="flex items-center mt-3">
                                    <x-base.button id="clear-filter" class="w-32 ml-auto"
                                                   variant="secondary" type="button">
                                        Сбросить
                                    </x-base.button>
                                    <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                        Фильтр
                                    </x-base.button>
                                </div>
                            </div>
                        </form>
                    </x-base.popover.panel>
                </x-base.popover>
            </div>
        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-10 text-center">
                            <input id="check-all" class="form-check-input" type="checkbox"  value="" />
                        </x-base.table.th>
                        <x-base.table.th class="w-10 text-center whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="w-32 text-center whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="w-40 text-center whitespace-nowrap">КАТЕГОРИЯ</x-base.table.th>
                        <x-base.table.th class="w-32 text-center whitespace-nowrap">ЦЕНА</x-base.table.th>
                        <x-base.table.th class="w-32 text-right whitespace-nowrap">НАЛИЧИЕ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($products as $product)
                        @include('admin.product.product._list', ['product' => $product])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
        <div class="col-span-12 mt-3 flex">
            <x-base.tom-select id="actions" class="w-52 bg-white">
                <option value="not" disabled selected>Действия</option>
                <option value="draft">В черновик</option>
                <option value="published">Опубликовать</option>
                <option value="remove">Удалить</option>
            </x-base.tom-select>
            <button id="action-check" type="button" class="btn btn-primary-soft ml-2"
                    data-route="{{ route('admin.product.action') }}"
                    disabled>Применить</button>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите удалить товар?<br>Этот процесс не может быть отменен.')->show() }}

    {{ $products->links('admin.components.paginator', ['pagination' => $pagination]) }}
<script>
    let clearFilter = document.getElementById('clear-filter');
    clearFilter.addEventListener('click', function () {
        window.location.href = window.location.href.split("?")[0];
    });

    let checkAll = document.getElementById('check-all');
    let checkProducts = document.querySelectorAll('.check-products');
    let actionsSelect = document.getElementById('actions');
    let actionButton = document.getElementById('action-check');

    //Выделить все элементы, снять все элементы
    checkAll.addEventListener('change', function () {
        Array.from(checkProducts).forEach(function (checkProduct) {
            checkProduct.checked = checkAll.checked;
        });
        _button();
    });


    //при снятии/выделении элемента checkProducts менять checkAll, если один элемент выделен, то actionButton активен, иначе disabled
    Array.from(checkProducts).forEach(function (checkProduct) {
        checkProduct.addEventListener('change', function () {
            checkAll.checked = _check_all() === 1;
            _button();
        });
    });

    //если выбран элемент actionsSelect сделать активным  actionButton
    actionsSelect.addEventListener('change', function () {
        _button();
    });

    function _check_all() {  //Проверка комбинация нажаты все, ни одной или несколько чекбоксов
        let _check = false, _uncheck = false;
        Array.from(checkProducts).forEach(function (checkProduct) {
            if (checkProduct.checked) {
                _check = true;
            } else {
                _uncheck = true;
            }
        });

        if (_check === true && _uncheck === true) return 0; //'Есть нажатые и не нажатые';
        if (_check === false && _uncheck === true) return -1; //'Все не нажаты';
        if (_check === true && _uncheck === false) return 1; //'Все нажаты';
    }

    //обработка actionButton
    actionButton.addEventListener('click', function () {
        let route = actionButton.dataset.route;
        let data = {action: actionsSelect.value, ids: []};

        Array.from(checkProducts).forEach(function (checkProduct) {
            if (checkProduct.checked) data.ids.push(checkProduct.value);
        });
        //console.log(data);

        setAjax(route, data);
    });

    function _button() {
        if (actionsSelect.value !== 'not' && _check_all() >= 0) {
            actionButton.disabled = false;
        } else  {
            actionButton.disabled = true;
        }
    }



    function setAjax(route, data) {
        let _params = '_token=' + '{{ csrf_token() }}' + '&data=' + JSON.stringify(data);
        let request = new XMLHttpRequest();
        request.open('POST', route);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let data = JSON.parse(request.responseText);
                if (data.error !== undefined) {
                    //Notification
                    window.notification('Ошибка!', data.error, 'danger');
                } else {
                    window.location.reload();
                }
            }
        };
    }

</script>
@endsection
