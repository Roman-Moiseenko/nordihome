<tr class="intro-x">
    <td class=""><a href="{{ route('admin.delivery.truck.show', $truck) }}"
                        class="font-medium whitespace-nowrap">{{ $truck->name }}</a></td>
    <td class="text-center">{{ $truck->weight }}</td>
    <td class="text-center">{{ $truck->volume }}</td>
    <td class="text-center">{{ $truck->getNameWorker() }}</td>
    <td class="text-center"><x-yesNo status="{{ $truck->active }}" lucide="" class="justify-center"/></td>

    <td class="table-report__action w-56">
        <div class="flex justify-center items-center">

            <a class="flex items-center mr-3" href="#"
               onclick="event.preventDefault(); document.getElementById('form-toggle-{{ $truck->id }}').submit();">
                @if($truck->isActive())
                    <x-base.lucide icon="copy-x" class="w-4 h-4"/> Draft
                @else
                    <x-base.lucide icon="copy-check" class="w-4 h-4"/> Activeted
                @endif
            </a>
            <form id="form-toggle-{{ $truck->id }}" method="post" action="{{ route('admin.delivery.truck.toggle', $truck) }}">
                @csrf
            </form>

            <a class="flex items-center mr-3" href="{{ route('admin.delivery.truck.edit', $truck) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.delivery.truck.destroy', $truck) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </td>
</tr>
