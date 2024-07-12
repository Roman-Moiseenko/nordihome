<x-base.table.tr>
    <x-base.table.td class="text-center  font-medium"><a href="{{ route('admin.order.refund.show', $refund) }}">{{ $refund->htmlDate() }}</a></x-base.table.td>
    <x-base.table.td class="whitespace-nowrap text-center">{{ $refund->order->htmlNum() . ' от ' . $refund->order->htmlDate() }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ price($refund->amount) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $refund->getQuantity() }} шт.</x-base.table.td>
    <x-base.table.td class="text-center font-medium">{{ $refund->staff->fullname->getFullName() }}</x-base.table.td>
</x-base.table.tr>
