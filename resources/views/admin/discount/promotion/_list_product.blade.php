<x-base.table.tr>
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="text-center"><a href="{{ route('admin.product.edit', $product) }}"
                                     class="font-medium whitespace-nowrap">{{ $product->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center whitespace-nowrap">
        <span class="text-right font-medium ml-auto mr-3 text-danger"><s>{{ $product->getLastPrice() }}</s></span>
        <input class="promotion-product form-control form-input w-40 ml-3" type="number" placeholder="Новая цена"
               data-route="{{ route('admin.discount.promotion.set-product', [$promotion, $product]) }}"
               value="{{ $product->pivot->price }}"> ₽

    </x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">

            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-group" data-route = {{ route('admin.discount.promotion.del-product', [$promotion, $product]) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>

        </div>
    </x-base.table.td>
</x-base.table.tr>

