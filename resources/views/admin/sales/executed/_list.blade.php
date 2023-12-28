<x-base.table.tr>
    <x-base.table.td class="w-52 font-medium"><a href="{{ route('admin.sales.executed.show', $order) }}">{{ $order->htmlNum() }}</a></x-base.table.td>
    <x-base.table.td class="w-40 font-medium"><a href="{{ route('admin.sales.executed.show', $order) }}">{{ $order->created_at->translatedFormat('d F Y') }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->amount }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->discount }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->coupon }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->total }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->status->value() }}</x-base.table.td>
    <x-base.table.td class="text-center font-medium"><a href="{{ route('admin.users.show', $order->user) }}">{{ $order->user->email }}</a></x-base.table.td>

</x-base.table.tr>
