<x-base.table.tr>
    <x-base.table.td class="w-52">
{{ dd($item) }}
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ '' }}" alt="{{ '$item->product->name' }}">
        </div>

    </x-base.table.td>
    <x-base.table.td class="w-40"> {{ '$item->product->name' }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ '$item[quantity]' }}</x-base.table.td>



</x-base.table.tr>
