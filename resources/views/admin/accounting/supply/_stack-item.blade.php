<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.product.edit', $stack->product) }}"
           class="font-medium whitespace-nowrap">{{ $stack->product->name }}</a>
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $stack->quantity }}</x-base.table.td>
    <x-base.table.td class="text-center">
        @if(!is_null($stack->orderItem))
            <a href="{{ route('admin.order.show', $stack->orderItem->order) }}" class="text-success font-medium" target="_blank">{{ $stack->comment }}</a>
        @else
        {{ $stack->comment . ' (' . $stack->storage->name . ')' }}
        @endif
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $stack->staff->fullname->getFullName() }}</x-base.table.td>
    <x-base.table.td class="text-center">
        @foreach($distributors as $distributor)
            @if($distributor->isProduct($stack->product))
                <div>{{ $distributor->name }}</div>
            @endif
        @endforeach
    </x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if(is_null($stack->orderItem))
                <a class="flex items-center text-danger" href="#"
                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.accounting.supply.del-stack', $stack) }}
                ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                    Delete </a>
            @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
