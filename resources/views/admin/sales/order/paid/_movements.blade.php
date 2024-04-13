<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-32 text-center">Склад прибытия</div>
    <div class="w-32 text-center">Склад убытия</div>
    <div class="w-32 text-center">Статус</div>
</div>
@foreach($order->movements as $i => $movement)
    <a href="{{ route('admin.accounting.movement.show', $movement) }}" target="_blank">
        <div class="box flex items-center p-2">
            <div class="w-20 text-center">{{ $i + 1 }}</div>
            <div class="w-32 text-center">{{ $movement->storageOut->name }}</div>
            <div class="w-32 text-center">{{ $movement->storageIn->name }}</div>
            <div class="w-32 text-center">{{ $movement->statusHTML() }}</div>
        </div>
    </a>
@endforeach
