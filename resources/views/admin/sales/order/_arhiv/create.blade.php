@extends('layouts.side-menu')

@section('subcontent')

    <script>
        let freeProducts = [];
        let preorderProducts = [];
        let orderAdditions = [];
        let userData = {};

        function updateAmount() {
            let amountFree = document.getElementById('free-amount');
            let amountPreorder = document.getElementById('preorder-amount');
            let amountProducts = document.getElementById('amount-products');
            let amountAdditions = document.getElementById('additions-amount');
            let amountInfoAdditions = document.getElementById('amount-additions');
            let amountTotal = document.getElementById('amount-total');

            let amountWeight= document.getElementById('amount-weight');
            let amountVolume= document.getElementById('amount-volume');

            let sum1 = _getSum(freeProducts);
            let sum2 = _getSum(preorderProducts);
            amountFree.value = sum1.sum;
            amountPreorder.value = sum2.sum;
            amountProducts.innerText = (sum1.sum + sum2.sum);

            amountWeight.innerText = (sum1.weight + sum2.weight);
            amountVolume.innerText = (sum1.volume + sum2.volume);

            let sum3 = _sumAdditions();
            amountAdditions.value = sum3;
            amountInfoAdditions.innerText = sum3;
            amountTotal.innerText = (sum1.sum + sum2.sum + sum3);
        }

        function _getSum(_array) {
            let sum = 0;
            let weight = 0;
            let volume = 0;
            _array.forEach(function (product) {
                if (product.promotion !== undefined && product.promotion !== 0) {
                    sum += product.promotion * product.count;
                } else {
                    sum += product.cost * product.count;
                }
                weight += product.weight * product.count;
                volume += product.volume * product.count;
            })
            return {sum: sum, weight: weight, volume: volume};
        }

        function _sumAdditions() {
            let _sum = 0;
            orderAdditions.forEach(function (addition) {
                _sum += addition.amount;
            });
            return _sum;
        }
    </script>

    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создать новый заказ
        </h2>
    </div>
    <div class="grid grid-cols-11 gap-x-6 pb-20">
        <!-- ORDER -->
        <div class="col-span-11 lg:col-span-9">
            @foreach($menus as $n => $menu)
                <div id="{{ $menu['anchor'] }}" data-is-top="{{ $n == 'common' ? 1 : 0 }}"
                     class="intro-y box p-5 mt-5 block-menus-order">
                    <div class="rounded-md border border-slate-200/60 p-5">
                        <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                            <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/> {{ $menu['caption'] }}
                        </div>
                        <div class="mt-5">
                            @include('admin.sales.order.blocks.' . $menu['include'])
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-span-2 hidden lg:block">
            <div class="fixed fixed-top pt-5">
                <ul
                    class="relative text-slate-500 before:absolute before:left-0 before:z-[-1] before:h-full before:w-[2px] before:bg-slate-200 before:content-[''] before:dark:bg-darkmode-600">
                    @foreach($menus as $n => $menu)
                        <li id="li-{{ $menu['anchor'] }}" class="li-menus-order mb-4 border-l-2 border-primary pl-5
        {{ ($n == 'common') ? 'border-primary text-primary font-medium' : 'border-transparent' }}">
                            <a href="#{{ $menu['anchor'] }}">{{ $menu['caption'] }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-5 flex flex-col justify-end gap-2 md:flex-row">
                    <x-base.button id="submit-order" class="w-full py-3" type="button" variant="primary"
                                   data-route="{{ route('admin.sales.order.store') }}">Сохранить
                    </x-base.button>
                </div>


                <div
                    class="relative mt-10 rounded-md border border-info bg-info/20 p-5 dark:border-0 dark:bg-darkmode-600">
                    <x-base.lucide class="absolute top-0 right-0 mt-5 mr-3 h-12 w-12 text-warning/80"
                                   icon="line-chart"/>
                    <h2 class="text-lg font-medium">Общая информация</h2>

                    <div class="mt-2 leading-relaxed text-slate-600 dark:text-slate-500">
                        <div class="">
                            <span>Сумма за товар </span><span class="font-medium" id="amount-products">0</span> ₽
                        </div>
                        <div class="mt-2">
                            <span>Сумма за услуги </span><span class="font-medium" id="amount-additions">0</span> ₽
                        </div>
                        <div class="mt-2">
                            <span>К оплате </span><span class="font-medium" id="amount-total">0</span> ₽
                        </div>
                    </div>

                    <div class="text-sm mt-4">
                        <div class="">
                            <span>Общий вес груза </span><span class="font-medium" id="amount-weight">0</span> кг
                        </div>
                        <div class="mt-2">
                            <span>Общий объем груза </span><span class="font-medium" id="amount-volume">0</span> м3
                        </div>
                    </div>
                    <div class="text-xs mt-3">
                        Примечание. Здесь будет информационный текст и правила заполнения
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="form-create-order" method="post" action="{{ route('admin.sales.order.store') }}">
        @csrf
        <input id="input-data-order" type="hidden" name="data" value="">
    </form>
    <script>


        //Scrolling and ActiveMenu
        let blocksScroll = document.querySelectorAll('.block-menus-order');
        let menusScroll = document.querySelectorAll('.li-menus-order');
        let submitOrder = document.getElementById('submit-order');

        const classesSelect = ['border-primary', 'dark:border-primary', 'text-primary', 'font-medium'];
        const classesUnSelect = ['border-transparent', 'dark:border-transparent'];

        window.addEventListener('scroll', function () {
            Array.from(blocksScroll).forEach(function (blockScroll) {
                const _t = blockScroll.getBoundingClientRect().top;
                if (_t > 20 && _t < 120 && blockScroll.getAttribute('data-is-top') === '0') {
                    updateMenus(blockScroll.getAttribute('id'))
                }
            });
        });

        function updateMenus(idBlockScroll) {
            Array.from(menusScroll).forEach(function (menuScroll) {
                if (menuScroll.getAttribute('id') === 'li-' + idBlockScroll) {
                    menuScroll.classList.remove(...classesUnSelect);
                    menuScroll.classList.add(...classesSelect);
                } else {
                    if (menuScroll.classList.contains('border-primary')) {
                        menuScroll.classList.remove(...classesSelect);
                        menuScroll.classList.add(...classesUnSelect);
                    }
                }
            });
            Array.from(blocksScroll).forEach(function (blockScroll) {
                if (blockScroll.getAttribute('id') === idBlockScroll) {
                    blockScroll.setAttribute('data-is-top', '1');
                } else {
                    blockScroll.setAttribute('data-is-top', '0');
                }
            });
        }

        submitOrder.addEventListener('click', function () {
            let hasError = false;
            //Заполняем данные о пользователе
            let delivery, storage;
            Array.from(document.querySelectorAll('.delivery-input')).forEach(function (_input) {
                if (_input.checked === true) delivery = Number(_input.value);
            });
            Array.from(document.querySelectorAll('#storages input')).forEach(function (_input) {
                if (_input.checked === true) storage = Number(_input.value);
            });

            let userData_id = userData.id; //Запомним id, если есть
            userData = {
                id: userData_id,
                delivery: delivery,
                email: document.getElementById('input-user-email').value,
                local: document.getElementById('input-delivery-local').value,
                name: document.getElementById('input-user-name').value,
                payment: document.getElementById('user-payment').value,
                phone: document.getElementById('input-user-phone').value,
                region: document.getElementById('input-delivery-region').value,
                storage: storage
            }

            //Проверка на заполнение
            if (userData.delivery === undefined || userData.payment === '' ||
                userData.email === '' || userData.phone === '' ||
                (userData.local === '' && userData.region === '' && userData.storage === undefined)) {
                hasError = true;
                window.notification('Ошибка заполнения данных', 'Вы не заполнили все поля о клиенте', 'danger');
            }
            if (freeProducts.length === 0 && preorderProducts.length === 0) {
                hasError = true;
                window.notification('Ошибка заполнения данных', 'Вы не заполнили товары', 'danger');
            }
            if (orderAdditions.length !== 0 && _sumAdditions() === 0) {
                hasError = true;
                window.notification('Ошибка заполнения данных', 'Вы не указали стоимость услуг', 'danger');
            }
            if (freeProducts.length !== 0 && _getSum(freeProducts).sum === 0) {
                hasError = true;
                window.notification('Ошибка заполнения данных', 'Неверное количество товаров в наличии', 'danger');
            }
            if (preorderProducts.length !== 0 && _getSum(preorderProducts).sum === 0) {
                hasError = true;
                window.notification('Ошибка заполнения данных', 'Неверное количество товаров в предзаказе', 'danger');
            }

            let _request = {
                free: freeProducts,
                preorder: preorderProducts,
                additions: orderAdditions,
                user: userData
            };

            if (hasError) return true;
            //window.notification('Ура!!', 'Вы правильно заполнили все поля', 'success');
            document.getElementById('input-data-order').value = JSON.stringify(_request);
            document.getElementById('form-create-order').submit();
            return true;
        });
    </script>
@endsection

