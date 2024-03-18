<x-base.table.tr>
    <x-base.table.td class="">
        {{ $cron->created_at->format('d-m-Y') }}
    </x-base.table.td>
    <x-base.table.td class="text-center"><a href="{{ route('admin.analytics.cron.show', $cron) }}" {{ $cron->event }}</x-base.table.td>

</x-base.table.tr>
