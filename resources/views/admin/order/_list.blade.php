<x-base.table.tr>
    <x-base.table.td class="text-center">{!! $item['opl'] !!}</x-base.table.td>
    <x-base.table.td class="text-center">{!! $item['otg'] !!}</x-base.table.td>
    <x-base.table.td class=""><a href="{{ $item['url'] }}" class="font-medium text-success">{{ $item['number'] }}</a></x-base.table.td>
    <x-base.table.td class=""><a href="{{ $item['url'] }}" class="font-medium text-success">{{ $item['date'] }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['manager'] }}</x-base.table.td>
    <x-base.table.td class="text-center">
        {{ $item['user'] }}
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['amount'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{!! $item['status_html'] !!}</x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3 text-warning" href="{{ $item['copy'] }}"
               onclick="event.preventDefault(); document.getElementById('copy-{{ $item['id'] }}').submit();">
                <x-base.lucide icon="copy" class="w-4 h-4"/>Copy
            </a>
            <form id="copy-{{ $item['id'] }}" method="post" action="{{ $item['copy'] }}">
                @csrf
            </form>
            @if($item['has_cancel'])
            <a class="flex items-center mr-3 text-danger" href="{{ $item['canceled'] }}"
               onclick="event.preventDefault(); document.getElementById('cancel-{{ $item['id'] }}').submit();">
                <x-base.lucide icon="ban" class="w-4 h-4"/>Cancel
            </a>
                <form id="cancel-{{ $item['id'] }}" method="post" action="{{ $item['canceled'] }}">
                    @csrf
                </form>
            @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
