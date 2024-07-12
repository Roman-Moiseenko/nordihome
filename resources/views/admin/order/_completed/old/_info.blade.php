<livewire:admin.order.user-info :order="$order" :edit="false"/>

<h2 class=" mt-3 font-medium">Информация о заказе</h2>
<div class="box p-3 flex flex-col items-center lg:items-start mt-2">

    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="contact"
                       class="w-4 h-4"/>&nbsp;{{ $order->getManager()->fullname->getFullName() }} -
        менеджер
    </div>
    <div class="flex mt-3">
        <div class="">
            <span>Общий вес груза </span><span class="font-medium" id="weight">{{ $order->getWeight() }} кг</span>
        </div>
        <div class="ml-3">
            <span>Общий объем груза </span><span class="font-medium" id="volume">{{ $order->getVolume() }} м3</span>
        </div>
    </div>
    <div class="form-control mt-4">
        <label class="form-check-label" for="order-comment">Комментарий</label>
        <input id="order-comment" class="form-control" type="text" name="comment"
               value="{{ $order->comment }}" readonly
        >
    </div>
</div>
<h2 class=" mt-3 font-medium">Действия</h2>
<div class="box flex p-3 lg:justify-start buttons-block items-start">
    <button class="btn btn-warning-soft" onclick="document.getElementById('form-order-copy').submit();">Скопировать</button>
    <form id="form-order-copy" method="post" action="{{ route('admin.order.copy', $order) }}">
        @csrf
    </form>
</div>

