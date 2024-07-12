<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-32 text-center">Дата</div>
    <div class="w-32 text-center">Сумма</div>
    <div class="w-32 text-center">Способ оплаты</div>
    <div class="w-32 text-center">Документ</div>
</div>
@foreach($order->payments as $i => $payment)
    <div class="box flex items-center p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-32 text-center">{{ $payment->created_at->format('d-m-Y H:i') }}</div>
        <div class="w-32 text-center">{{ price($payment->amount) }}</div>
        <div class="w-32 text-center">{{ $payment->methodHTML() }}</div>
        <div class="w-32 text-center">{{ $payment->document }}</div>
    </div>
@endforeach
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center"></div>
    <div class="w-32 text-left">Всего оплачено: </div>
    <div class="w-32 text-center">{{ price($order->getPaymentAmount()) }}</div>
</div>

<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center"></div>
    <div class="w-32 text-left">Не оплачено: </div>
    <div class="w-32 text-center">{{ price($order->getTotalAmount() - $order->getPaymentAmount()) }}</div>
</div>
