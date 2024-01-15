<x-base.table.tr>
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            @if(!empty($prod_attribute->getImage()))
            <img class="rounded-full" src="{{ $prod_attribute->getImage() }}" alt="{{ $prod_attribute->name }}">
            @endif
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-40"><a href="{{ route('admin.product.attribute.show', $prod_attribute) }}"
                                      class="font-medium whitespace-nowrap">{{ $prod_attribute->name }}</a></x-base.table.td>
    <x-base.table.td class=""> |
        @foreach($prod_attribute->categories as $category)
            {{ $category->name . ' | ' }}
        @endforeach
    </x-base.table.td>
    <x-base.table.td class="">{{ $prod_attribute->group->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ \App\Modules\Product\Entity\Attribute::ATTRIBUTES[$prod_attribute->type] }}</x-base.table.td>
    <x-base.table.td class="text-center"><x-yesNo status="{{ $prod_attribute->filter }}" lucide="" class="justify-center"/></x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.product.attribute.edit', $prod_attribute) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.attribute.destroy', $prod_attribute) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
