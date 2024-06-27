<livewire:admin.sales.order.user-info :order="$order" />

<div class="grid grid-cols-12 gap-x-6">

    <div class="col-span-12">
        <div class="font-medium text-center lg:text-left lg:mt-5 text-base">Информация о заказе</div>
        <div class="flex flex-row items-center lg:items-start mt-4">
            <div class="truncate sm:whitespace-normal flex items-center">
                <x-base.lucide icon="history" class="w-4 h-4"/>&nbsp;{{ $order->statusHtml() }}
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4">
                <x-base.lucide icon="badge-russian-ruble" class="w-4 h-4"/>&nbsp;{{ price($order->getSellAmount()) }}
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4">
                <x-base.lucide icon="sigma" class="w-4 h-4"/>&nbsp;{{ price($order->getTotalAmount()) }}
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4">
                <x-base.lucide icon="package" class="w-4 h-4"/>&nbsp;{{ count($order->items) }} товаров
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4">
                <x-base.lucide icon="boxes" class="w-4 h-4"/>&nbsp;{{ $order->getQuantity() }} шт.
            </div>
            @if(!$order->isStatus(\App\Modules\Order\Entity\Order\OrderStatus::PAID) && !$order->isCanceled())
                <div class="truncate sm:whitespace-normal flex items-center ml-4">
                    <x-base.lucide icon="baggage-claim" class="w-4 h-4"/>&nbsp;{{ $order->getReserveTo() }}
                </div>
            @endif
        </div>
        <div class="flex flex-col items-center lg:items-start mt-2">

                <div class="truncate sm:whitespace-normal flex items-center">
                    <x-base.lucide icon="contact"
                                   class="w-4 h-4"/>&nbsp;{{ $order->getManager()->fullname->getFullName() }} -
                    менеджер
                </div>

        </div>
    </div>
    <div class="col-span-12 mt-3">
        <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="message-square"
                       class="w-4 h-4"/>&nbsp;Комментарий: {{ $order->comment }}
        </div>
    </div>
</div>
