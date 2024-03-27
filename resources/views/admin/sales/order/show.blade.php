@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $order->htmlDate() . ' ' .$order->htmlNum() }}
            </h1>
        </div>
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
                            @include('admin.sales.order.blocks.' . $menu['include'] . '-show')
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-span-2 hidden lg:block">
            <div class="fixed fixed-top pt-5">
                <ul
                    class="relative text-slate-500 before:absolute before:left-0 before:z-[-1] before:h-full before:w-[2px] before:bg-slate-200 before:content-['']">
                    @foreach($menus as $n => $menu)
                        <li id="li-{{ $menu['anchor'] }}" class="li-menus-order mb-4 border-l-2 border-primary pl-5
                            {{ ($n == 'common') ? 'border-primary text-primary font-medium' : 'border-transparent' }}">
                            <a href="#{{ $menu['anchor'] }}">{{ $menu['caption'] }}</a>
                        </li>
                    @endforeach
                </ul>
                @include('admin.sales.order.blocks.actions')

                <div class="relative mt-10 rounded-md border border-info bg-info/20 p-5">
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


    @if($order->isManager())
        {{ \App\Forms\ModalDelete::create('Вы уверены?',
            'Вы действительно хотите удалить платеж?<br>Этот процесс не может быть отменен.', 'id-delete-payment')->show() }}
    @endif

    <script>

        //Scrolling and ActiveMenu
        let blocksScroll = document.querySelectorAll('.block-menus-order');
        let menusScroll = document.querySelectorAll('.li-menus-order');
        let submitOrder = document.getElementById('submit-order');

        let inputUpdateData = document.querySelectorAll('.update-data-ajax');

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

        Array.from(inputUpdateData).forEach(function (input) {
            input.addEventListener('change', function () {
                let value = input.value;
                let route = input.getAttribute('data-route');
                let field = 'value';
                setAjax(route, field, value)
            });
        });


        function setAjax(route, field, value) {
            let _params = '_token=' + '{{ csrf_token() }}' + '&' + field + '=' + value;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let _data = JSON.parse(request.responseText);
                    console.log(_data);
                    //TODO Обновляем данные по сумме заказа
                    /*if (_data === true) {
                        location.reload();
                    } else {
                        console.log(_data);
                    }*/
                }
            };
        }
    </script>
@endsection
