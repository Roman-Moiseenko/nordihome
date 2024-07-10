@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                Возврат на заказ {{ $order->htmlDate() . ' ' .$order->htmlNum() }}
            </h1>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-x-6 pb-10">
        <div class="col-span-12 lg:col-span-12">
            <div class="box p-5 mt-5 block-menus-order">
                <div class="rounded-md border border-slate-200/60 p-5">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                        <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                        Информация о Заказе
                    </div>
                    <div class="mt-5">
                        <livewire:admin.sales.order.user-info :order="$order" :edit="false"/>

                        <div>
                            <h2 class=" mt-3 font-medium">Информация о платежах</h2>
                            <div class="box p-3 flex flex-col items-center flex-wrap lg:items-start mt-4">
                                <div class="truncate sm:whitespace-normal flex items-center my-auto">
                                    Оплачено за заказ&nbsp;{{ price($order->getPaymentAmount()) }}
                                </div>
                                <div class="truncate sm:whitespace-normal flex items-center mt-1 my-auto">
                                    Товару выдано на&nbsp;{{ price($order->getExpenseAmount()) }}
                                </div>
                                <div class="truncate sm:whitespace-normal flex items-center mt-1 my-auto">
                                    Сумма на возврат&nbsp;{{ price($order->getPaymentAmount() - $order->getExpenseAmount()) }}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form class="col-span-12" method="post" action="{{ route('admin.sales.refund.store', $order) }}">
            @csrf
            <div class="col-span-12 lg:col-span-12">
                <div class="box p-5 mt-5 block-menus-order">
                    <div class="rounded-md border border-slate-200/60 p-5">
                        <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                            <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                            Товары и услуги на возврат
                        </div>
                        <div class="mt-5">
                            @if($order->isCompleted())
                                Если заказ завершен - список товаров с выбором и/или кол-вом = 0, если >0 то возврат<br>
                                ****<br>
                                @foreach($order->items as $item)
                                    <div class="box-in-box flex items-center p-2">
                                        <div class="w-1/4">
                                            {{ $item->product->name }}
                                        </div>
                                        <div class="w-40">
                                            {{ price($item->sell_cost) }}
                                        </div>
                                        <div>
                                            <div class="w-40 input-group">
                                                <input type="number" class="form-control" name="item[{{ $item->id }}].quantity"
                                                       value="0"
                                                       min="0" max="{{ $item->quantity }}"
                                                >
                                                <div id="addition->amount" class="input-group-text">шт.</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Если заказ не завершен -->
                                <!-- список без редактирования всех не выданных товаров -->
                                @foreach($order->items as $item)
                                    @if($item->getRemains() > 0)
                                        <div class="box-in-box flex items-center p-2">
                                            <div class="w-1/4">
                                                {{ $item->product->name }}
                                            </div>
                                            <div class="w-40">
                                                {{ price($item->sell_cost) }}
                                            </div>
                                            <div class="w-40 input-group">
                                                <input type="number" class="form-control" name="item[{{ $item->id }}].quantity"
                                                       value="{{ $item->getRemains() }}"
                                                       min="0" max="{{ $item->getRemains() }}" readonly
                                                >
                                                <div id="addition->amount" class="input-group-text">шт.</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <!-- список без редактирования всех не оказанных услуг -->
                                @foreach($order->additions as $addition)
                                    @if($addition->getRemains() > 0)
                                        <div class="box-in-box flex items-center p-2">
                                            <div class="w-1/4">
                                                {{ $addition->purposeHTML() }}
                                            </div>
                                            <div class="w-40">
                                                {{ price($addition->amount) }}
                                            </div>
                                            <div class="w-40 input-group">
                                                <input type="number" class="form-control" name="addition[{{ $addition->id }}].amount"
                                                       value="{{ $addition->getRemains() }}"
                                                       min="0" max="{{ $addition->getRemains() }}" readonly
                                                >
                                                <div id="addition->amount" class="input-group-text">₽</div>
                                            </div>

                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-12">
                <div class="box p-5 mt-5 block-menus-order">
                    <div class="rounded-md border border-slate-200/60 p-5">
                        <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium">
                            <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/>
                            Платежи
                        </div>
                        <div class="mt-5">
                            @foreach($order->payments as $payment)
                                <div class="box-in-box flex items-center p-2">
                                    <div class="w-1/4">
                                        {{ $payment->methodHTML() }}
                                    </div>
                                    <div class="w-40">
                                        {{ price($payment->amount) }}
                                    </div>
                                    <div class="w-40 input-group">
                                        <input type="number" class="form-control" name="payment[{{ $payment->id }}]amount"
                                               value="{{ 0 }}"
                                               min="0" max="{{ $payment->amount }}"
                                        >
                                        <div id="addition->amount" class="input-group-text">₽</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="box mt-3 p-5 flex">
                <input class="form-control w-40" name="number" type="text" placeholder="Номер документа">
                <input class="form-control ml-2" name="comment" type="text" placeholder="Основание возврата / Комментарий">
            </div>


            <button class="btn btn-success mt-3" type="submit">
                Сохранить
            </button>
        </form>
    </div>


    <script>



    </script>
@endsection
