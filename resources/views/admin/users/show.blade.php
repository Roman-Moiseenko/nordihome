@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h1 class="text-xl font-medium mr-auto">
            {{ $user->email }} еще данные?
        </h1>
    </div>
    <div class="intro-y box px-5 pt-5 mt-5">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="flex flex-1 flex-col justify-center items-center lg:items-start ml-4 mt-4">
                <div class="truncate sm:whitespace-normal font-medium text-lg">
                    {{ $user->email }} еще данные?
                </div>
                <div class="truncate sm:whitespace-normal flex items-center mt-3">
                    <i data-lucide="mail" width="24" height="24"
                       class="lucide lucide-mail w-4 h-4 mr-2"></i> {{ $user->email }}</div>
                <div class="truncate sm:whitespace-normal flex items-center mt-3">
                    <i data-lucide="phone" width="24" height="24" class="lucide lucide-phone w-4 h-4 mr-2"></i>
                    {{ $user->phone }} </div>
                <div class="truncate sm:whitespace-normal flex mt-3">
                    <i data-lucide="home" width="24" height="24" class="lucide lucide-home w-4 h-4 mr-2"></i>
                    {{ 'Калининградская область' }} </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3">Покупки клиента</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <i data-lucide="package" width="24" height="24" class="lucide lucide-mail w-4 h-4 mr-2"></i>
                        {{ '5' }} </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i data-lucide="russian-ruble" width="24" height="24"
                           class="lucide lucide-mail w-4 h-4 mr-2"></i>
                        {{ '9 999 999' }} </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i data-lucide="clock" width="24" height="24" class="lucide lucide-mail w-4 h-4 mr-2"></i>
                        {{ '12.03.2023' }} </div>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-5">Sales Growth</div>
                <div class="flex items-center justify-center lg:justify-start mt-2">
                    <div class="mr-2 w-20 flex"> USP: <span class="ml-3 font-medium text-success">+23%</span></div>
                    <div class="w-3/4">
                        <div class="h-[55px]">
                            <canvas class="simple-line-chart-1 -mr-5" width="733" height="137"
                                    style="display: block; box-sizing: border-box; height: 54.8px; width: 293.2px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center lg:justify-start">
                    <div class="mr-2 w-20 flex"> STP: <span class="ml-3 font-medium text-danger">-2%</span></div>
                    <div class="w-3/4">
                        <div class="h-[55px]">
                            <canvas class="simple-line-chart-2 -mr-5" width="733" height="137"
                                    style="display: block; box-sizing: border-box; height: 54.8px; width: 293.2px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
        <div class="pt-3 pb-2">
            <h2 class="text-lg">Заказы клиента</h2>
        </div>
        <table class="table table-report -mt-2 dropdown-table">
            <!-- foreach ($user->orders() as $order) -->
            @for($j =1; $j < random_int(3, 7); $j++)
                <tr class="intro-x zoom-in tr-dropdown" target="show-{{$j}}" show="hide">
                    <td class="w-10">{{'Дата заказа'}}</td>
                    <td class="">{{'Кол-во товаров'}}</td>
                    <td class="">{{'Сумма заказа'}}</td>
                    <td class="">{{'Доставка (куда и чем)'}}</td>
                    <td class="">{{'Статус'}}</td>
                    <td class="w-10 text-right">
                        <div>
                            <i data-lucide="chevron-down" width="24" height="24"
                               class="lucide lucide-chevron-down w-4 h-4"></i>
                        </div>
                    </td>
                </tr>
                <tr id="show-{{$j}}" class="hidden">
                    <td colspan="6">
                        <table class="table table-hover">
                            <tbody>
                            <!-- foreach ($order->items() as $item) -->
                            @for($i =1; $i < random_int(2, 10); $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ 'IMG' }}</td>
                                    <td>{{'Название'}}</td>
                                    <td>{{'Кол-во'}}</td>
                                    <td>{{'Цена'}}</td>
                                    <td>{{'Сумма'}}</td>
                                </tr>
                            @endfor
                            <!-- endforeach -->
                            </tbody>
                        </table>
                    </td>
                </tr>
        @endfor
        <!-- endforeach -->
        </table>
    </div>

    <script>

        let elements = document.querySelectorAll(".tr-dropdown");
        Array.from(elements).forEach(function (element) {
            element.addEventListener('click', function () {
                let _show = element.getAttribute('show');
                let _for = element.getAttribute('target');
                let dropTable = document.getElementById(_for);
                let td_chevron = element.querySelector('.text-right > div');
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
