<x-base.table.tr>
    <x-base.table.td class="">
        {{ $activity->created_at->format('d-m-Y') }}
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $activity->staff->fullname->getFullName() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $activity->action }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $activity->url }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $activity->request_params }}</x-base.table.td>
</x-base.table.tr>
