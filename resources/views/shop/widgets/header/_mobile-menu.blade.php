@php
    use App\Modules\Shop\Application\DTOs\CategoryTreeClientData;
    use App\Modules\Shop\Application\DTOs\RoomTreeClientData;

    /** @var CategoryTreeClientData[]|RoomTreeClientData[] $categories */
    /** @var string $entity - category или room    */
@endphp

@foreach($categories as $category)
    <li>
        <a href="{{ route('shop.' . $entity . '.view', $category->slug) }}">
            <img src="{{ (empty($item->image)) ? '\images\no-image.jpg' : $item->image }}" alt="{{ $category->name }}">
            <div>{{ $category->name }}</div>
        </a>
    </li>
@endforeach
