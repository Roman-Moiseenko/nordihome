<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-32 text-center">Дата</div>
    <div class="w-32 text-center">Сумма</div>
    <div class="w-32 text-center">Склад выдачи</div>
    <div class="w-32 text-center">Статус</div>
</div>

@foreach($order->expenses as $i => $expense)
    <a href="{{ route('admin.sales.expense.show', $expense) }}">
        <div class="box-in-box flex items-center p-2">
            <div class="w-20 text-center">{{ $i + 1 }}</div>
            <div class="w-32 text-center">{{ $expense->created_at->format('d-m-Y H:i') }}</div>
            <div class="w-32 text-center">{{ price($expense->getAmount()) }}</div>
            <div class="w-32 text-center">{{ $expense->storage->name }}</div>
            <div class="w-32 text-center">{{ $expense->statusHTML() }}</div>
        </div>
    </a>
@endforeach
