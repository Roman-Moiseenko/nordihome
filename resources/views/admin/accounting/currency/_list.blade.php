<tr>
    <td><a href="{{ route('admin.accounting.currency.show', $currency) }}" class="font-medium">{{ $currency->name }}</a></td>
    <td class="text-center whitespace-nowrap">{{ $currency->sign }}</td>
    <td class="text-center whitespace-nowrap">{{ $currency->exchange }}</td>
    <td class="table-report__action w-72">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.accounting.currency.edit', $currency) }}" title="Edit">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#" title="Delete"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.accounting.currency.destroy', $currency) }}>
                <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </td>
</tr>





