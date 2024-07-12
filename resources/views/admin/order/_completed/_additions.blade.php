<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12">
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
                        <input id="" type="number" class="form-control text-right update-data-ajax"
                               value="{{ $addition->amount }}" aria-describedby="addition->amount"
                               min="0" data-num="0"  readonly

                        >
                        <div id="addition->amount" class="input-group-text">₽</div>
                    </div>

                    <div class="w-56 text-center">{{ $addition->comment }}</div>
                    <div class="w-20 text-center">



                    </div>
                </div>
        @endforeach

        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center"></div>
            <div class="w-56 text-center">ИТОГО</div>
            <div class="w-40 text-center">
                <div class="w-40 input-group">
                    <input id="additions-amount" type="number" class="form-control text-right" value="{{ $order->getAdditionsAmount() }}" aria-describedby="input-preorder-amount" readonly>
                    <div id="input-preorder-amount" class="input-group-text">₽</div>
                </div>
            </div>
        </div>
    </div>
</div>
