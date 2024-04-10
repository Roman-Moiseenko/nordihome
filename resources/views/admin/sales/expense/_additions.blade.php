<x-base.table.tr>
    <x-base.table.td class="w-56">{{ $addition->orderAddition->purposeHTML() }}</x-base.table.td>
    <x-base.table.td class="w-40 text-center">{{ $addition->amount }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $addition->comment }}</x-base.table.td>
</x-base.table.tr>
