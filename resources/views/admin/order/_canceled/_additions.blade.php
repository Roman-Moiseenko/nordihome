<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12">
        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center">№ п/п</div>
            <div class="w-56 text-center">Услуга</div>
            <div class="w-40 text-center">Сумма</div>
            <div class="w-56 text-center">Примечание</div>
        </div>

        @foreach($order->additions as $i => $addition)
                <div class="box flex items-center p-2">
                    <div class="w-20 text-center">{{ $i + 1 }}</div>
                    <div class="w-56 text-left">{{ $addition->purposeHTML() }}</div>
                    <div class="w-40 text-center">{{ price($addition->amount) }}</div>

                    <div class="w-56 text-center">{{ $addition->comment }}</div>
                </div>
        @endforeach

        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center"></div>
            <div class="w-56 text-left">ИТОГО</div>
            <div class="w-40 text-center">{{ price($order->getAdditionsAmount()) }}</div>
        </div>
    </div>
</div>
