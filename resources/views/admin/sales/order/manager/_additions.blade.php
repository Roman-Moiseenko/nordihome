<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12">

        @if($order->isManager())
            <form method="post" action="{{ route('admin.sales.order.add-addition', $order) }}">
                @csrf
                <div class="mx-3 flex w-full mb-5">

                    <select id="addition-purpose" name="purpose" class="form-select w-full lg:w-56">
                        <option value="0"></option>
                        @foreach(\App\Modules\Order\Entity\Order\OrderAddition::PAYS as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {{ \App\Forms\Input::create('amount', ['placeholder' => 'Стоимость', 'value' => 0, 'class' => 'ml-2 w-40'])
                        ->type('number')->min_max(0, null)->required()->group_text('₽', false)->show() }}
                    {{ \App\Forms\Input::create('comment', ['placeholder' => 'Примечание', 'class' => 'ml-2 w-1/4'])
                        ->show() }}
                    <x-base.button id="add-addition" type="submit" variant="primary" class="ml-3">Добавить услугу в
                        документ
                    </x-base.button>
                </div>
            </form>
        @endif

        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center">№ п/п</div>
            <div class="w-56 text-center">Услуга</div>
            <div class="w-40 text-center">Сумма</div>
            <div class="w-56 text-center">Примечание</div>
            <div class="w-20 text-center">-</div>
        </div>

        @foreach($order->additions as $i => $addition)
            <livewire:admin.sales.order.manager-addition :addition="$addition" :i="$i"/>
        @endforeach

        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center"></div>
            <div class="w-56 text-center">ИТОГО</div>
            <div class="w-40 text-center">
                <div class="w-40 input-group">
                    <input id="additions-amount" type="number" class="form-control text-right"
                           value="{{ $order->getAdditionsAmount() }}" aria-describedby="input-preorder-amount" readonly>
                    <div id="input-preorder-amount" class="input-group-text">₽</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('addition-order-update', (event) => {
            document.getElementById('additions-amount').value = event.addition_amount;
        });
    });
</script>
