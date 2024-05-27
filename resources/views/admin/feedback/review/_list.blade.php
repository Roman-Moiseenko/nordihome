<x-base.table.tr>
    <x-base.table.td class="w-40 font-medium"><a href="{{ route('admin.product.show', $review->product) }}">{{ $review->product->name }}</a></x-base.table.td>
    <x-base.table.td class="w-40 font-medium"><a href="{{ route('admin.users.show', $review->user) }}">{{ $review->user->fullname->getFullName() }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $review->rating }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $review->htmlDate() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $review->statusHtml() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $review->text }}</x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.feedback.review.show', $review) }}">
                <x-base.lucide icon="eye" class="w-4 h-4"/>
                View </a>
            @if($review->isModerated())
            <a class="flex items-center mr-3" href="{{ route('admin.feedback.review.published', $review) }}"
               onclick="event.preventDefault();document.getElementById('review-published-{{ $review->id }}').submit();">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Published </a>
            <form id="review-published-{{ $review->id }}" method="post" action="{{ route('admin.feedback.review.published', $review) }}">
                @csrf
            </form>

            <a class="flex items-center text-danger" href="{{ route('admin.feedback.review.blocked', $review) }}"
               onclick="event.preventDefault();document.getElementById('review-blocked-{{ $review->id }}').submit();">
                <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Blocked </a>
                <form id="review-blocked-{{ $review->id }}" method="post" action="{{ route('admin.feedback.review.blocked', $review) }}">
                    @csrf
                </form>
            @endif
        </div>
    </x-base.table.td>

</x-base.table.tr>
