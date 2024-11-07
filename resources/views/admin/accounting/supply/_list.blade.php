<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.supply.show', $item['id']) }}" class="font-medium text-success">
            {{ $item['date'] }}
        </a>
    </x-base.table.td>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.supply.show', $item['id']) }}" class="font-medium text-success">
            {{ $item['number'] }}
        </a>
    </x-base.table.td>
    <x-base.table.td class="text-center">
        {{ $item['distributor'] }}
    </x-base.table.td>
    <x-base.table.td class="text-center"><x-yes-no :status="$item['completed']" /></x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['quantity'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['comment'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['staff'] }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">

            <a class="flex items-center mr-3 text-warning" href="#"
               onclick="event.preventDefault(); document.getElementById('copy-{{ $item['id'] }}').submit();">
                <x-base.lucide icon="copy" class="w-4 h-4"/>Copy
            </a>
            <form id="copy-{{ $item['id'] }}" method="post" action="{{ route('admin.accounting.supply.copy', $item['id']) }}">
                @csrf
            </form>
            @if($item['is_delete'])
                <a class="flex items-center text-danger" href="#" data-tw-toggle="modal"
                   data-tw-target="#delete-confirmation-modal"
                   data-route="{{ route('admin.accounting.supply.destroy', $item['id']) }}">
                    <x-base.lucide icon="trash-2" class="w-4 h-4"/>Delete
                </a>
            @endif

        </div>
    </x-base.table.td>
</x-base.table.tr>
