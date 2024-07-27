<tr class="">
    <td class=""><a href="{{ route('admin.worker.show', $worker) }}"
                        class="font-medium whitespace-nowrap">{{ $worker->fullname->getFullName() }}</a></td>
    <td class="text-center">{{ $worker->postHtml() }}</td>
    <td class="text-center">{{ phone($worker->phone) }}</td>
    <td class="text-center">{{ $worker->telegram_user_id }}</td>
    <td class="text-center">{{ $worker->storage->name }}</td>
    <td class="text-center"><x-yesNo status="{{ $worker->active }}" lucide="" class="justify-center"/></td>

    <td class="table-report__action w-56">
        <div class="flex justify-center items-center">

            <a class="flex items-center mr-3" href="#"
               onclick="event.preventDefault(); document.getElementById('form-toggle-{{ $worker->id }}').submit();">
                @if($worker->isActive())
                    <x-base.lucide icon="copy-x" class="w-4 h-4"/> Draft
                @else
                    <x-base.lucide icon="copy-check" class="w-4 h-4"/> Activeted
                @endif
            </a>
            <form id="form-toggle-{{ $worker->id }}" method="post" action="{{ route('admin.worker.toggle', $worker) }}">
                @csrf
            </form>

            <a class="flex items-center mr-3" href="{{ route('admin.worker.edit', $worker) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.worker.destroy', $worker) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </td>
</tr>
