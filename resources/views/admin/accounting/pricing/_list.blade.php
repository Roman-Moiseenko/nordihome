<x-base.table.tr>
    <x-base.table.td class=""><a href="{{ route('admin.accounting.pricing.show', $item['id']) }}" class="font-medium text-success">{{ $item['date'] }}</a>
    </x-base.table.td>
    <x-base.table.td class=""><a href="{{ route('admin.accounting.pricing.show', $item['id']) }}" class="font-medium text-success">{{ $item['number'] }}</a>
    </x-base.table.td>
    <x-base.table.td class="text-center">
        <x-yesNo :status="$item['completed']" lucide=""/>
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['staff'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['comment'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item['arrival'] }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if(!$item['completed'])

                <a class="flex items-center mr-3" href="{{ route('admin.accounting.pricing.show', $item['id']) }}">
                    <x-base.lucide icon="check-square" class="w-4 h-4"/>
                    Edit
                </a>
                <a class="flex items-center text-danger" href="#" data-tw-toggle="modal"
                   data-tw-target="#delete-confirmation-modal" data-route= {{ route('admin.accounting.pricing.destroy', $item['id']) }}>
                    <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                    Delete
                </a>

            @else
                <a class="flex items-center text-warning mr-3" href="#"
                   onclick="event.preventDefault(); document.getElementById('copy-pricing-{{ $item["id"] }}').submit();"
                >
                    <x-base.lucide icon="copy" class="w-4 h-4"/>
                    Copy
                </a>
                <form id="copy-pricing-{{ $item["id"] }}" method="post" action="{{ route('admin.accounting.pricing.copy', $item['id'])  }}">
                    @csrf
                </form>
            @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
