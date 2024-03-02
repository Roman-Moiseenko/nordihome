<x-base.table.tr>
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $item->product->getImage() }}" alt="{{ $item->product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-40"><a href="{{ route('admin.product.show', $item->product) }}"
                                     class="font-medium whitespace-nowrap">{{ $item->product->name }}</a></x-base.table.td>

    <x-base.table.td class="text-center"> {{ ($item->quantity == 0) ? 'отмена' : $item->quantity}} {{ !is_null($item->first_quantity) ? '(' . $item->first_quantity . ')' : '' }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ price($item->base_cost) }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ ($item->base_cost == $item->sell_cost) ? '-' : price($item->sell_cost) }} <br><span class="fs-8"> {{ $item->discountName() }} </span> </x-base.table.td>
    <x-base.table.td class="text-center">  </x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if($order->isManager())
                <x-base.form-input name="new-quantity" data-id="{{ $item->id }}" class="w-20" type="hidden" value="{{ $item->quantity }}"
                                   min="0" max="{{ $item->quantity }}" placeholder=""/>
            @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
