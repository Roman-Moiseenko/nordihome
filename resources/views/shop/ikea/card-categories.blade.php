@php
    use App\Modules\Shop\Application\DTOs\IkeaTreeClientData;
    /** @var IkeaTreeClientData[] $categories */
    /** @var int $currentId */
@endphp

    <!-- //TODO Меню Категорий Сделать свернутым -->
@foreach($categories as $category)
    <div class="accordion" id="ikea-categories-accordion">
        <div class="accordion-item m-b_10">
            <div class="accordion-header">
                <div class="f-w_600 m-b_20 f-z_21 accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ikea-categories-collapse01">{{ $category->name }}</div>
            </div>
            <div id="ikea-categories-collapse01" class="accordion-collapse collapse">
                <ul class="m-b_20">
                    @foreach($category->children as $child)
                        <li>
                            <a href="{{ route('shop.ikea.view', $child->slug) }}"
                               class="{{ $currentId == $child->id  ? 'active' : '' }}">
                                {{ $child->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endforeach
