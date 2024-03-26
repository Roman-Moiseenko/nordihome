<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12">

        @if($order->isManager())
        <div class="mx-3 flex w-full mb-5">
            <select id="addition-purpose" name="" class="form-select w-full lg:w-56">
                <option value="0"></option>
                @foreach(\App\Modules\Order\Entity\Order\OrderAddition::PAYS as $code => $name)
                    <option value="{{ $code }}">{{ $name }}</option>
                @endforeach
            </select>
            {{ \App\Forms\Input::create('addition-amount', ['placeholder' => 'Стоимость', 'value' => 0, 'class' => 'ml-2 w-40'])->type('number')->group_text('₽', false)->show() }}
            {{ \App\Forms\Input::create('addition-comment', ['placeholder' => 'Примечание', 'class' => 'ml-2 w-1/4'])->show() }}
            <x-base.button id="add-addition" type="button" variant="primary" class="ml-3">Добавить услугу в документ
            </x-base.button>
        </div>
        @endif
        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center">№ п/п</div>
            <div class="w-56 text-center">Услуга</div>
            <div class="w-40 text-center">Сумма</div>
            <div class="w-56 text-center">Примечание</div>
            <div class="w-20 text-center">-</div>
        </div>

        @foreach($order->additions as $i => $addition)
                <div class="box flex items-center p-2">
                    <div class="w-20 text-center">{{ $i + 1 }}</div>
                    <div class="w-56 text-center">{{ $addition->purposeHTML() }}</div>
                    <div class="w-40 input-group">
                        <input id="" type="number" class="form-control text-right quantity-input"
                               value="{{ $addition->amount }}" aria-describedby="input-quantity"
                               min="0" data-num="0" @if(!$order->isManager()) readonly @endif>
                        <div id="input-quantity" class="input-group-text">шт.</div>
                    </div>

                    <div class="w-56 text-center">{{ $addition->comment }}</div>
                    <div class="w-20 text-center">
                        @if($order->isManager())
                            <button class="btn btn-outline-danger ml-6 product-remove" data-num = "{{ $i }}"
                                    data-id="{{ $addition->id }}" data-array="free" type="button">X</button>
                        @endif
                    </div>
                </div>
        @endforeach

        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center"></div>
            <div class="w-56 text-center">ИТОГО</div>
            <div class="w-40 text-center">
                <div class="w-40 input-group">
                    <input id="additions-amount" type="number" class="form-control text-right" value="" aria-describedby="input-preorder-amount" readonly>
                    <div id="input-preorder-amount" class="input-group-text">₽</div>
                </div>
            </div>
        </div>
    </div>
</div>
