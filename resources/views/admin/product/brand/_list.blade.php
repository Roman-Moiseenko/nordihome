<tr class="intro-x">
    <td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $brand->getPhoto() }}" alt="{{ $brand->name }}">
        </div>
    </td>
    <td class="w-40"><a href="{{ route('admin.product.brand.show', $brand) }}"
                        class="font-medium whitespace-nowrap">{{ $brand->name }}</a></td>
    <td>{{ $brand->description }}</td>
    <td><a href="{{ $brand->url }}" target="_blank" class="text-primary">{{ $brand->url }}</a></td>
    <td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.product.brand.edit', $brand) }}">
                <i data-lucide="check-square" class="w-4 h-4"></i>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.brand.destroy', $brand) }}
               >
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Delete </a>
        </div>
    </td>
</tr>
