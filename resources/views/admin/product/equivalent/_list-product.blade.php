<tr>
    <td class="w-20"><div class="image-fit w-10 h-10"><img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}"></div></td>

    <td><a href="{{ route('admin.product.show', $product) }}" class="font-medium">{{ $product->name }}</a></td>

    <td class="table-report__action w-72">
        <div class="flex justify-center items-center">

            <a class="flex items-center text-danger" href="#" title="Delete"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.equivalent.del-product', [$equivalent, $product]) }}>
                <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </td>
</tr>



