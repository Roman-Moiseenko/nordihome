<x-base.table.tr>
    <x-base.table.td class="w-40 font-medium">{{ $payment->created_at->format('d-m-Y H:i') }}</x-base.table.td>
    <x-base.table.td class="w-40 font-medium">{{ price($payment->amount) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $payment->order->htmlDate() . ' ' . $payment->order->htmlNum() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $payment->getUserFullName() }}</x-base.table.td>
    <x-base.table.td class="text-center font-medium">{{ $payment->staff->fullname->getFullName() }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.sales.payment.edit', $payment) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.sales.payment.destroy', $payment) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
