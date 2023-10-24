<tr class="intro-x">
    <td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $prod_attribute->getImage() }}" alt="{{ $prod_attribute->name }}">
        </div>
    </td>
    <td class="w-40"><a href="{{ route('admin.product.attribute.show', $prod_attribute) }}"
                        class="font-medium whitespace-nowrap">{{ $prod_attribute->name }}</a></td>
    <td>{{ $prod_attribute->type }}</td>
    <td></td>
    <td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.product.attribute.edit', $prod_attribute) }}">
                <i data-lucide="check-square" class="w-4 h-4"></i>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.attribute.destroy', $prod_attribute) }}
               >
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Delete </a>
        </div>
    </td>
</tr>
