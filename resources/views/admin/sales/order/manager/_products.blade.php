<div class="mx-3 flex w-full mb-5">
    @if($order->isParser())
        <x-searchAddParser event="add-parser" quantity="1" width="100"/>
    @else
        <x-searchAddProduct event="add-product" quantity="1" parser="1" width="100" show-stock="1" published="1"/>
    @endif
</div>
<livewire:admin.sales.order.manager-items :order="$order"/>
<livewire:admin.sales.order.manager-amount :order="$order"/>
