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
                    {{ $user->email }}
                </div>
                <div class="truncate sm:whitespace-normal flex items-center mt-3">
                    <livewire:admin.user.edit.fullname :user="$user"/>
                </div>
                <div class="truncate sm:whitespace-normal flex items-center mt-3">
                    <livewire:admin.user.edit.email :user="$user"/>
                </div>
                <div class="truncate sm:whitespace-normal flex items-center mt-3">
                    <livewire:admin.user.edit.phone :user="$user"/>
                </div>
                <div class="truncate sm:whitespace-normal flex mt-3">
                    <livewire:admin.user.edit.delivery :user="$user"/>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3">Покупки клиента</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <i data-lucide="package" width="24" height="24" class="lucide lucide-mail w-4 h-4 mr-2"></i>
                        {{ $all . ' (' . $completed . ')' }} </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i data-lucide="russian-ruble" width="24" height="24"
                           class="lucide lucide-mail w-4 h-4 mr-2"></i>
                        {{ price($amount_all) . ' (' . price($amount_completed) . ')' }} </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i data-lucide="clock" width="24" height="24" class="lucide lucide-mail w-4 h-4 mr-2"></i>
                        {{ $user->getLastOrder()->htmlDate() }} </div>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-5">Sales Growth</div>
                <div class="flex items-center justify-center lg:justify-start mt-2">
                    <div class="mr-2 w-20 flex"> Данные анализа: <span class="ml-3 font-medium text-success">+23%</span></div>
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
            @foreach ($user->orders as $j => $order)
                <tr class="intro-x zoom-in tr-dropdown" target="show-{{$j}}" show="hide">
                    <td class=""><a href="{{ route('admin.sales.order.show', $order) }}" class="font-medium text-success">{{ $order->htmlNumDate() }}</a></td>
                    <td class="">Товаров: {{ $order->getQuantity() }}</td>
                    <td class="">Сумма к оплате: {{ $order->getTotalAmount() }}</td>
                    <td class="">{{ '' }}</td>
                    <td class="">{{ $order->statusHtml() }}</td>
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
                            @foreach ($order->items as $i => $item)
                                <tr>
                                    <td class="w-10">{{ $i + 1 }}</td>
                                    <td class="w-20"><img src="{{ $item->product->getImage() }}"></td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }} шт.</td>
                                    <td>{{ price($item->sell_cost) }}</td>
                                    <td>{{ price($item->sell_cost * $item->quantity) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
        @endforeach
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
