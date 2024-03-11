<x-base.table.tr>
    <x-base.table.td class="w-20">
        {{ $payment->created_at->format('d-m') }}
    </x-base.table.td>
    <x-base.table.td class="w-40">{{ price($payment->amount) }}</x-base.table.td>
    <x-base.table.td class="text-center"> {{ $payment->nameType() }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ $payment->purposeHTML() }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ $payment->comment }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ is_null($payment->paid_at) ? '-' : $payment->paid_at->format('d-m H:i') }} </x-base.table.td>
    <x-base.table.td class="w-40">
    @if($order->isManager())
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#id-delete-payment" data-route = {{ route('admin.sales.order.del-payment', $payment) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
    @endif
    </x-base.table.td>
</x-base.table.tr>
