<x-base.table.tr>
    <x-base.table.td class="text-center  font-medium"><a href="{{ route('admin.sales.refund.show', $refund) }}">{{ $refund->htmlDate() }}</a></x-base.table.td>
    <x-base.table.td class="whitespace-nowrap text-center">{{ $refund->order->htmlNum() . ' от ' . $refund->order->htmlDate() }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ price($refund->getRefundAmount()) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $refund->getQuantity() }} шт.</x-base.table.td>
    <x-base.table.td class="text-center">{{ $refund->statusHtml() }}</x-base.table.td>
    <x-base.table.td class="text-center font-medium">{{ $refund->staff->fullname->getFullName() }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="*****">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                ???? </a>

        </div>
    </x-base.table.td>
</x-base.table.tr>
