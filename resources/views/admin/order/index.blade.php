@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Заказы
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <form method="get" action="{{ route('admin.order.index') }}" class="flex w-full">
                <div>
                    <input type="radio" class="btn-check" name="status" id="option1" autocomplete="off"
                           value="all" onclick="this.form.submit();" @if($filter == 'all') checked @endif>
                    <label class="btn btn-primary" for="option1">Все</label>
                    <input type="radio" class="btn-check" name="status" id="option2" autocomplete="off"
                           value="new" onclick="this.form.submit();" @if($filter == 'new') checked @endif>
                    <label class="btn btn-success" for="option2">Новые
                        @if($filter_count['new'] != 0)<span>{{ $filter_count['new'] }}</span> @endif
                    </label>
                    <input type="radio" class="btn-check" name="status" id="option3" autocomplete="off"
                           value="awaiting" onclick="this.form.submit();" @if($filter == 'awaiting') checked @endif>
                    <label class="btn btn-success" for="option3">На оплате
                        @if($filter_count['awaiting'] != 0)<span>{{ $filter_count['awaiting'] }}</span> @endif
                    </label>

                    <input type="radio" class="btn-check" name="status" id="option4" autocomplete="off"
                           value="at-work" onclick="this.form.submit();" @if($filter == 'at-work') checked @endif>
                    <label class="btn btn-success" for="option4">В работе
                        @if($filter_count['at-work'] != 0)<span>{{ $filter_count['at-work'] }}</span> @endif
                    </label>
                    <input type="radio" class="btn-check" name="status" id="option5" autocomplete="off"
                           value="canceled" onclick="this.form.submit();" @if($filter == 'canceled') checked @endif>
                    <label class="btn btn-secondary" for="option5">Отмененные</label>
                    <input type="radio" class="btn-check" name="status" id="option6" autocomplete="off"
                           value="completed" onclick="this.form.submit();" @if($filter == 'completed') checked @endif>
                    <label class="btn btn-secondary" for="option6">Завершенные</label>
                </div>
                <div class="ml-auto">
                    <x-base.tom-select id="select-staff" name="staff_id"
                                       class="w-72 bg-white" data-placeholder="Выберите ответственного">
                        <option value=""></option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}"
                                {{ $staff->id == $filters['staff_id'] ? 'selected' : ''}} >
                                {{ $staff->fullname->getShortName() }}
                            </option>
                        @endforeach
                    </x-base.tom-select>
                </div>

            </form>
        </div>
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button data-tw-toggle="modal" data-tw-target="#modal-create-order" class="btn btn-primary shadow-md mr-2"
                    type="button">Создать заказ
            </button>
            {{ $orders->links('admin.components.count-paginator') }}

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
                                <input type="hidden" name="search" value="1" />
                                <input class="form-control" name="user" placeholder="Клиент,Телефон,ИНН,Email"
                                       value="{{ $filters['user'] }}" autocomplete="off">
                                <x-base.tom-select class="w-full bg-white mt-1" name="condition"
                                                   data-placeholder="Состояние заказа"
                                >
                                    <option value="" disabled selected>Состояние заказа</option>
                                    @foreach(\App\Modules\Order\Entity\Order\OrderStatus::STATUSES as $key => $name)
                                        <option value="{{ $key }}"
                                            {{ $key == $filters['condition'] ? 'selected' : ''}} >
                                        {{ $name }}</option>
                                    @endforeach
                                </x-base.tom-select>

                                <x-base.tom-select id="select-staff" name="staff_id"
                                                   class="w-full bg-white mt-1" data-placeholder="Выберите ответственного">
                                    <option value="" disabled selected>Выберите ответственного</option>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ $staff->id == $filters['staff_id'] ? 'selected' : ''}} >
                                            {{ $staff->fullname->getShortName() }}
                                        </option>
                                    @endforeach
                                </x-base.tom-select>
                                <input class="form-control mt-1" name="comment" placeholder="Комментарий"
                                       value="{{ $filters['comment'] }}" autocomplete="off">
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
                        <x-base.table.th class="w-10 whitespace-nowrap"></x-base.table.th>
                        <x-base.table.th class="w-32 whitespace-nowrap">№</x-base.table.th>
                        <x-base.table.th class="w-32 whitespace-nowrap text-center">ДАТА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОТВЕТСТВЕННЫЙ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КЛИЕНТ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ТИП</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ИТОГО</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">СТАТУС</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($orders as $order)
                        @include('admin.order._list', ['order' => $order])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    {{ $orders->links('admin.components.paginator', ['pagination' => $pagination]) }}


    <x-base.dialog id="modal-create-order" staticBackdrop>
        <x-base.dialog.panel>
            <input type="hidden" id="route-search-user" value="{{ route('admin.order.search-user') }}">
            <form id="modal-destroy-form" action="{{ route('admin.order.store') }}" method="POST">
                @csrf
                <x-base.dialog.title>
                    <h2 class="mr-auto text-base font-medium">Создать заказ</h2>
                </x-base.dialog.title>

                <x-base.dialog.description class="grid grid-cols-12 gap-4 gap-y-3">
                    <x-base.form-input id="input-id" type="hidden" name="user_id"/>
                    <div class="col-span-12">
                        <x-base.form-label for="input-phone">Телефон</x-base.form-label>
                        <x-base.form-input id="input-phone" class="input-search-user mask-phone" type="text" name="phone" placeholder="8 (___) ___-__-__" required />
                    </div>
                    <div class="col-span-12">
                        <x-base.form-label for="input-email">Почта</x-base.form-label>
                        <x-base.form-input id="input-email" class="input-search-user mask-email" type="text" name="email" placeholder="example@gmail.com" required />
                    </div>
                    <div class="col-span-12">
                        <x-base.form-label for="input-name">Имя</x-base.form-label>
                        <x-base.form-input id="input-name" type="text" name="name" placeholder="Имя"/>
                    </div>
                    <div class="">
                        <div class="form-check items-center">
                            <x-base.form-input id="input-parser" type="checkbox" name="parser" class="form-check-input" placeholder="Парсер"/>
                            <x-base.form-label for="input-parser" class="mb-0 ml-2 cursor-pointer">Парсер</x-base.form-label>
                        </div>
                    </div>
                </x-base.dialog.description>

                <x-base.dialog.footer>
                    <x-base.button id="modal-cancel" class="mr-1 w-24" data-tw-dismiss="modal" type="button" variant="outline-secondary">Отмена</x-base.button>
                    <x-base.button class="w-24" type="submit" variant="primary">Создать</x-base.button>
                </x-base.dialog.footer>
            </form>
        </x-base.dialog.panel>
    </x-base.dialog>

    <script>
        let inputSearchUser = document.querySelectorAll('.input-search-user');
        Array.from(inputSearchUser).forEach(function (input) {
            input.addEventListener('change', function () {
                let data = input.value;
                if (data !== '') findUser(input.value);
            })
        });
        function findUser(data) {
            //AJAX
            let route = document.getElementById('route-search-user').value;
            let _params = '_token=' + '{{ csrf_token() }}' + '&data=' + data;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(request.responseText);
                    console.log(data);
                    if (data !== false) {
                        document.getElementById('input-id').value = data.id;
                        document.getElementById('input-phone').value = data.phone;
                        document.getElementById('input-email').value = data.email;
                        document.getElementById('input-name').value = data.name;
                    }
                } else {
                }
            };
        }
        let buttonCloseModal = document.getElementById('modal-cancel');
        buttonCloseModal.addEventListener('click', function () {
            document.getElementById('input-id').value = '';
            document.getElementById('input-phone').value = '';
            document.getElementById('input-email').value = '';
            document.getElementById('input-name').value = '';
            return true;
        });

    </script>

    <script>
        /* Filters */
        //TODO Фильтр по дате
        const urlParams = new URLSearchParams(window.location.search);

        let selectStaff = document.getElementById('select-staff');
        selectStaff.addEventListener('change', function () {
            let p = selectStaff.options[selectStaff.selectedIndex].value;
            urlParams.set('staff_id', p);
            window.location.search = urlParams;
        });


        let clearFilter = document.getElementById('clear-filter');
        clearFilter.addEventListener('click', function () {
            window.location.href = window.location.href.split("?")[0];
        });

    </script>
@endsection
