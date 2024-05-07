<livewire:admin.sales.order.user-info :order="$order" />

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


