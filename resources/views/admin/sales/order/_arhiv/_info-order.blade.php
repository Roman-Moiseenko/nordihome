<div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Информация о заказе</div>
<div class="flex flex-col justify-center items-center lg:items-start mt-4">
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="history" class="w-4 h-4"/>&nbsp;{{ $order->statusHtml() }}
    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="badge-russian-ruble" class="w-4 h-4"/>&nbsp;{{ price($order->total) }}
    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="sigma" class="w-4 h-4"/>&nbsp;{{ price($order->totalPayments()) }}
    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="package" class="w-4 h-4"/>&nbsp;{{ count($order->items) }} товаров
    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="boxes" class="w-4 h-4"/>&nbsp;{{ $order->getQuantity() }} шт.
    </div>

    @if(!$order->isStatus(\App\Modules\Order\Entity\Order\OrderStatus::PAID) && !$order->isCanceled())
        <div class="truncate sm:whitespace-normal flex items-center">
            <x-base.lucide icon="baggage-claim" class="w-4 h-4"/>&nbsp;{{ $order->getReserveTo() }}
        </div>
    @endif
    <div
        class="truncate sm:whitespace-normal flex items-center border-b w-100 border-slate-200/60 pb-2 mb-2"
        style="width: 100%;"></div>
    @if(!empty($order->getManager()))
        <div class="truncate sm:whitespace-normal flex items-center">
            <x-base.lucide icon="contact"
                           class="w-4 h-4"/>&nbsp;{{ $order->getManager()->fullname->getFullName() }} -
            менеджер
        </div>
    @endif
    @if(!empty($order->getLogger()))
        <div class="truncate sm:whitespace-normal flex items-center">
            <x-base.lucide icon="contact"
                           class="w-4 h-4"/>&nbsp;{{ $order->getLogger()->fullname->getFullName() }} -
            логист
        </div>
    @endif
</div>
