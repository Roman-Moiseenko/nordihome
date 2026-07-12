@php
    use App\Modules\Shop\Application\DTOs\IkeaTreeClientData;
    /** @var IkeaTreeClientData[] $categories */
    /** @var int $currentId */
@endphp

    <!-- //TODO Меню Категорий Сделать свернутым -->
@foreach($categories as $category)
    <h2>{{ $category->name }}</h2>
    <ul>
        @foreach($category->children as $child)
            <li>
                <a href="{{ route('shop.ikea.view', $child->slug) }}"
                   class="{{ $currentId == $child->id  ? 'active' : '' }}">
                    {{ $child->name }}
                </a>
            </li>
        @endforeach
    </ul>
@endforeach
