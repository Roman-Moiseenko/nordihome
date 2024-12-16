<tr class="">
    <td class="w-40"><a href="{{ route('admin.discount.discount.show', $discount) }}"
                        class="font-medium whitespace-nowrap">{{ $discount->name }}</a></td>
    <td class="w-20">{{ $discount->discount }} %</td>
    <td class="text-center">{{ $discount->caption() }}</td>
    <td class="text-center">{{ $discount->nameType() }}</td>
    <td class="text-center"><x-yesNo status="{{ $discount->isActive() }}" lucide="" class="justify-center"/></td>

    <td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if(!$discount->active())
            <a class="flex items-center mr-3" href="{{ route('admin.discount.discount.edit', $discount) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.discount.discount.destroy', $discount) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
            @endif
        </div>
    </td>
</tr>
