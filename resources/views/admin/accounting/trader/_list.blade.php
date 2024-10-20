<tr>
    <td class="w-20">-</td>
    <td><a href="{{ route('admin.accounting.trader.show', $trader) }}" class="font-medium">{{ $trader->name }}</a></td>
    <td> - </td>
    <td> - </td>
    <td> - </td>
    <td class="text-center"> - </td>
    <td class="table-report__action w-72">
        <div class="flex justify-center items-center">

            <a class="flex items-center mr-3" href="{{ route('admin.accounting.trader.edit', $trader) }}" title="Edit">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#" title="Delete"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.accounting.trader.destroy', $trader) }}>
                <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </td>
</tr>



