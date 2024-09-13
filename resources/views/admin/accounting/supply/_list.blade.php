<x-base.table.tr>
    <x-base.table.td class=""><a href="{{ $item['url'] }}" class="font-medium text-success">{{ $item['date'] }}</a></x-base.table.td>
    <x-base.table.td class=""><a href="{{ $item['url'] }}" class="font-medium text-success">{{ $item['number'] }}</a></x-base.table.td>
    <x-base.table.td class="text-center">
        {{ $item['distributor'] }}
    </x-base.table.td>
    <x-base.table.td class="text-center">{!! $item['status_html'] !!}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['quantity'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['comment'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['staff'] }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ $item['url'] }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>Edit
            </a>
            <a class="flex items-center text-danger" href="#" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ $item['destroy'] }}>
                <x-base.lucide icon="trash-2" class="w-4 h-4"/>Delete
            </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
