<x-base.table.tr class="">
    <x-base.table.td>
        <a href="{{ $item['url'] }}">
            <span class="font-medium">{{ $item['name'] }}</span>
            <br>
            <span class="text-slate-500 text-xs mt-0.5">{{ $item['phone'] }}</span>
        </a>
    </x-base.table.td>
    <x-base.table.td class="text-center">{!! $item['data']['last'] !!}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['data']['count'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['data']['amount'] }}</x-base.table.td>
    <x-base.table.td class="text-right">{{ $item['address']}}</x-base.table.td>
</x-base.table.tr>
