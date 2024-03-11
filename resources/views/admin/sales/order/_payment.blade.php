<x-base.table.tr>
    <x-base.table.td class="w-20">
        {{ $payment->created_at->format('d-m') }}
    </x-base.table.td>
    <x-base.table.td class="w-40">{{ price($payment->amount) }}</x-base.table.td>
    <x-base.table.td class="text-center">  </x-base.table.td>
    <x-base.table.td class="text-center"> {{ $payment->purposeHTML() }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ is_null($payment->paid_at) ? '-' : $payment->paid_at->format('d-m H:i') }} </x-base.table.td>
</x-base.table.tr>
