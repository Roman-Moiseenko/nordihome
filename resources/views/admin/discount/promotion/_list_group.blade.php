<x-base.table.tr>
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $group->getImage() }}" alt="{{ $group->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-40"><a href="{{ route('admin.product.group.show', $group) }}"
                                     class="font-medium whitespace-nowrap">{{ $group->name }}</a></x-base.table.td>

    <x-base.table.td class="text-center">{{ count($group->products) }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if($promotion->status() == \App\Modules\Discount\Entity\Promotion::STATUS_DRAFT)
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-group" data-route = {{ route('admin.discount.promotion.del-group', [$promotion, $group]) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
            @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
