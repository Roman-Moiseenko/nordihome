<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Услуга</div>
    <div class="w-32 text-center">Сумма</div>
</div>
@foreach($refund->additions as $i => $addition)
    <div class="box flex items-center p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-1/4 text-center">{{ $addition->orderAddition->purposeHTML() }}</div>
        <div class="w-32 text-center">{{ $addition->amount }}</div>
    </div>
@endforeach
