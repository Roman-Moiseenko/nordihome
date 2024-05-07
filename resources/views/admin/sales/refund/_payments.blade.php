<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Способ оплаты</div>
    <div class="w-32 text-center">Сумма возврата</div>
</div>
@foreach($refund->payments as $i => $payment)
    <div class="box flex items-center p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-1/4 text-center">{{ $payment->orderPayment->methodHTML() }}</div>
        <div class="w-32 text-center">{{ $payment->amount }}</div>
    </div>
@endforeach
