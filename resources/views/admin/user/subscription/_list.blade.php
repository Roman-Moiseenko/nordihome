<x-base.table.tr>
    <x-base.table.td class="w-40 font-medium"><a href="{{ route('admin.user.subscription.edit', $subscription) }}">{{ $subscription->name }}</a></x-base.table.td>
    <x-base.table.td class="w-56 font-medium">{{ $subscription->title }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $subscription->description }}</x-base.table.td>
    <x-base.table.td class="text-center"><x-yesNo status="{{ $subscription->published }}" lucide="" class="justify-center"/></x-base.table.td>
    <x-base.table.td class="text-center">{{ $subscription->users()->count() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $subscription->listener }}</x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if($subscription->published)
                <form action="{{ route('admin.user.subscription.draft', $subscription) }}" method="POST">
                    @csrf
                    <a class="flex items-center mr-3" href="#" onclick="this.parentNode.submit()">
                        <x-base.lucide icon="file-x" class="w-4 h-4"/>
                        Draft </a>
                </form>
            @else
                <form action="{{ route('admin.user.subscription.published', $subscription) }}" method="POST">
                    @csrf
                    <a class="flex items-center mr-3" href="#" onclick="this.parentNode.submit()">
                        <x-base.lucide icon="file-check" class="w-4 h-4"/>
                        Published </a>
                </form>
            @endif
                <a class="flex items-center mr-3" href="{{ route('admin.user.subscription.edit', $subscription) }}">
                    <x-base.lucide icon="check-square" class="w-4 h-4"/>
                    Edit </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
