<tr>
    <td class="w-20"><div class="image-fit w-10 h-10"><img class="rounded-full" src="{{ $storage->getImage() }}" alt="{{ $storage->name }}"></div></td>
    <td><a href="{{ route('admin.accounting.storage.show', $storage) }}" class="font-medium">{{ $storage->name }}</a></td>
    <td>{{ $storage->address }}</td>
    <td><x-yesNo status="{{ $storage->point_of_sale }}" lucide="" class="justify-center"/></td>
    <td><x-yesNo status="{{ $storage->point_of_delivery }}" lucide="" class="justify-center"/></td>
    <td class="text-center">{{ count($storage->items) }}</td>
    <td class="table-report__action w-72">
        <div class="flex justify-center items-center">

            <a class="flex items-center mr-3" href="{{ route('admin.accounting.storage.edit', $storage) }}" title="Edit">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>

        </div>
    </td>
</tr>



