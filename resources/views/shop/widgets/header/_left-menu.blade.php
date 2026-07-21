@php
    use App\Modules\Shop\Application\DTOs\CategoryTreeClientData;
    use App\Modules\Shop\Application\DTOs\RoomTreeClientData;

    /** @var CategoryTreeClientData[]|RoomTreeClientData[] $categories */
    /** @var string $entity - category или room    */
@endphp


@foreach($categories as $category)
    <a href="{{ route('shop.' . $entity . '.view', $category->slug) }}"
       class="nav-link" data-pane="{{ $category->slug }}" role="tab">
        {{ $category->name }}
    </a>
@endforeach
